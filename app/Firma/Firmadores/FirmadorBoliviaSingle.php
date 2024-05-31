<?php
namespace App\Firma\Firmadores;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

// require_once __DIR__ . '/../Exceptions/FirmaException.php';
// require_once('/../Exceptions/FirmaException.php');
require_once('FirmaException.php' );

class FirmadorBoliviaSingle
{
    /**
     * @var string
     */
    private $p12Path;
    /**
     * @var string
     */
    private $contrasenia;

    const BASE_TEMPLATE = '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">
  <SignedInfo>
    <SignatureMethod />
  </SignedInfo>
</Signature>';

    /** @var DOMElement|null */
    private $sigNode = null;

    private $xPathCtx = null;

    const XMLDSIGNS = 'http://www.w3.org/2000/09/xmldsig#';

    private $searchpfx = 'secdsig';

    private $cryptParams = array();

    private $passphrase = "";

    public function __construct(string $p12Path, string $contrasenia)
    {
        $this->p12Path = $p12Path;
        $this->contrasenia = $contrasenia;
    }

    /**
     * Firmar un xml
     *
     * @param string $xml Contenido del xml
     * @return string el XML firmado
     * @throws FirmaException
     */
    public function firmar(string $xml): string
    {
        $certs = [];
        $cert = openssl_pkcs12_read(file_get_contents($this->p12Path), $certs, $this->contrasenia);

        if (!$cert) {
            throw new FirmaException("No se pudo abrir el certificado.");
        }

        // Load the XML to be signed
        $doc = new DOMDocument();
        $doc->loadXML($xml);

        $this->create();

        $this->setCanonicalMethod();

        $this->addReference(
            $doc,
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments'),
            ['force_uri' => true]
        );

        $this->createKey(array('type' => 'private'));

        $this->passphrase = $this->contrasenia;


        $this->loadKey($certs['pkey']);

        $this->add509Cert($certs['cert']);

//        $objDSig->appendSignature();

        $this->sign($doc->documentElement);

        return $doc->saveXML();
    }

    private static function getRawThumbprint($cert)
    {

        $arCert = explode("\n", $cert);
        $data = '';
        $inData = false;

        foreach ($arCert as $curData) {
            if (!$inData) {
                if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) == 0) {
                    $inData = true;
                }
            } else {
                if (strncmp($curData, '-----END CERTIFICATE', 20) == 0) {
                    break;
                }
                $data .= trim($curData);
            }
        }

        if (!empty($data)) {
            return strtolower(sha1(base64_decode($data)));
        }

        return null;
    }

    private function loadKey($key, $isFile = false, $isCert = false)
    {
        if ($isFile) {
            $this->key = file_get_contents($key);
        } else {
            $this->key = $key;
        }
        if ($isCert) {
            $this->key = openssl_x509_read($this->key);
            openssl_x509_export($this->key, $str_cert);
            $this->x509Certificate = $str_cert;
            $this->key = $str_cert;
        } else {
            $this->x509Certificate = null;
        }
        if ($this->cryptParams['library'] == 'openssl') {
            switch ($this->cryptParams['type']) {
                case 'public':
                    if ($isCert) {
                        /* Load the thumbprint if this is an X509 certificate. */
                        $this->X509Thumbprint = self::getRawThumbprint($this->key);
                    }
                    $this->key = openssl_get_publickey($this->key);
                    if (!$this->key) {
                        throw new Exception('Unable to extract public key');
                    }
                    break;

                case 'private':
                    $this->key = openssl_get_privatekey($this->key, $this->passphrase);
                    break;

                case'symmetric':
                    if (strlen($this->key) < $this->cryptParams['keysize']) {
                        throw new Exception('Key must contain at least ' . $this->cryptParams['keysize'] . ' characters for this cipher, contains ' . strlen($this->key));
                    }
                    break;

                default:
                    throw new Exception('Unknown type');
            }
        }
    }

    private function createKey($params = null)
    {
        $this->cryptParams['library'] = 'openssl';
        $this->cryptParams['method'] = 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256';
        $this->cryptParams['padding'] = OPENSSL_PKCS1_PADDING;
        $this->cryptParams['digest'] = 'SHA256';
        if (is_array($params) && !empty($params['type'])) {
            if ($params['type'] == 'public' || $params['type'] == 'private') {
                $this->cryptParams['type'] = $params['type'];
            }
        }
    }

    private function sign($appendToNode = null)
    {
        // If we have a parent node append it now so C14N properly works
        $this->xPathCtx = null;

        $document = $appendToNode->ownerDocument;
        $signatureElement = $document->importNode($this->sigNode, true);

        $appendToNode->insertBefore($signatureElement);

        $this->sigNode = $appendToNode->lastChild;

        if ($xpath = $this->getXPathObj()) {
            $query = "./secdsig:SignedInfo";
            $nodeset = $xpath->query($query, $this->sigNode);
            if ($sInfo = $nodeset->item(0)) {
                $query = "./secdsig:SignatureMethod";
                $nodeset = $xpath->query($query, $sInfo);
                $sMethod = $nodeset->item(0);
                $sMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256');
                $data = $this->canonicalizeData($sInfo, $this->canonicalMethod);
                $sigValue = base64_encode($this->signOpenSSL($data));
                $sigValueNode = $this->createNewSignNode('SignatureValue', $sigValue);
                if ($infoSibling = $sInfo->nextSibling) {
                    $infoSibling->parentNode->insertBefore($sigValueNode, $infoSibling);
                } else {
                    $this->sigNode->appendChild($sigValueNode);
                }
            }
        }
    }

    private function signOpenSSL($data)
    {
        $algo = OPENSSL_ALGO_SHA1;
        if (!empty($this->cryptParams['digest'])) {
            $algo = $this->cryptParams['digest'];
        }
        if (!openssl_sign($data, $signature, $this->key, $algo)) {
            throw new Exception('Failure Signing Data: ' . openssl_error_string() . ' - ' . $algo);
        }
        return $signature;
    }

    public function createNewSignNode($name, $value = null)
    {
        $doc = $this->sigNode->ownerDocument;
        if (!is_null($value)) {
            $node = $doc->createElementNS(self::XMLDSIGNS, $name, $value);
        } else {
            $node = $doc->createElementNS(self::XMLDSIGNS, $name);
        }
        return $node;
    }

    private function canonicalizeData($node, $canonicalmethod, $arXPath = null, $prefixList = null)
    {
        $exclusive = false;
        $withComments = false;
        switch ($canonicalmethod) {
            case 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315':
                $exclusive = false;
                $withComments = false;
                break;
            case 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments':
                $withComments = true;
                break;
            case 'http://www.w3.org/2001/10/xml-exc-c14n#':
                $exclusive = true;
                break;
            case 'http://www.w3.org/2001/10/xml-exc-c14n#WithComments':
                $exclusive = true;
                $withComments = true;
                break;
        }

        if (is_null($arXPath) && ($node instanceof DOMNode) && ($node->ownerDocument !== null) && $node->isSameNode($node->ownerDocument->documentElement)) {
            /* Check for any PI or comments as they would have been excluded */
            $element = $node;
            while ($refnode = $element->previousSibling) {
                if ($refnode->nodeType == XML_PI_NODE || (($refnode->nodeType == XML_COMMENT_NODE) && $withComments)) {
                    break;
                }
                $element = $refnode;
            }
            if ($refnode == null) {
                $node = $node->ownerDocument;
            }
        }

        return $node->C14N($exclusive, $withComments, $arXPath, $prefixList);
    }

    private function getXPathObj()
    {
        if (empty($this->xPathCtx) && !empty($this->sigNode)) {
            $xpath = new DOMXPath($this->sigNode->ownerDocument);
            $xpath->registerNamespace('secdsig', self::XMLDSIGNS);
            $this->xPathCtx = $xpath;
        }
        return $this->xPathCtx;
    }

    private function add509Cert($cert, $isPEMFormat = true, $isURL = false, $options = null)
    {
        if ($xpath = $this->getXPathObj()) {
            self::staticAdd509Cert($this->sigNode, $cert, $isPEMFormat, $isURL, $xpath, $options);
        }
    }

    public static function staticGet509XCerts($certs, $isPEMFormat = true)
    {
        if ($isPEMFormat) {
            $data = '';
            $certlist = array();
            $arCert = explode("\n", $certs);
            $inData = false;
            foreach ($arCert as $curData) {
                if (!$inData) {
                    if (strncmp($curData, '-----BEGIN CERTIFICATE', 22) == 0) {
                        $inData = true;
                    }
                } else {
                    if (strncmp($curData, '-----END CERTIFICATE', 20) == 0) {
                        $inData = false;
                        $certlist[] = $data;
                        $data = '';
                        continue;
                    }
                    $data .= trim($curData);
                }
            }
            return $certlist;
        } else {
            return array($certs);
        }
    }

    private static function staticAdd509Cert($parentRef, $cert, $isPEMFormat = true, $isURL = false, $xpath = null, $options = null)
    {
        if ($isURL) {
            $cert = file_get_contents($cert);
        }
        if (!$parentRef instanceof DOMElement) {
            throw new Exception('Invalid parent Node parameter');
        }
        $baseDoc = $parentRef->ownerDocument;

        if (empty($xpath)) {
            $xpath = new DOMXPath($parentRef->ownerDocument);
            $xpath->registerNamespace('secdsig', self::XMLDSIGNS);
        }

        $query = "./secdsig:KeyInfo";
        $nodeset = $xpath->query($query, $parentRef);
        $keyInfo = $nodeset->item(0);
        $dsig_pfx = '';
        if (!$keyInfo) {
            $pfx = $parentRef->lookupPrefix(self::XMLDSIGNS);
            if (!empty($pfx)) {
                $dsig_pfx = $pfx . ":";
            }
            $inserted = false;
            $keyInfo = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx . 'KeyInfo');

            $query = "./secdsig:Object";
            $nodeset = $xpath->query($query, $parentRef);
            if ($sObject = $nodeset->item(0)) {
                $sObject->parentNode->insertBefore($keyInfo, $sObject);
                $inserted = true;
            }

            if (!$inserted) {
                $parentRef->appendChild($keyInfo);
            }
        } else {
            $pfx = $keyInfo->lookupPrefix(self::XMLDSIGNS);
            if (!empty($pfx)) {
                $dsig_pfx = $pfx . ":";
            }
        }

        // Add all certs if there are more than one
        $certs = self::staticGet509XCerts($cert, $isPEMFormat);

        // Attach X509 data node
        $x509DataNode = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx . 'X509Data');
        $keyInfo->appendChild($x509DataNode);

        $issuerSerial = false;
        $subjectName = false;
        if (is_array($options)) {
            if (!empty($options['issuerSerial'])) {
                $issuerSerial = true;
            }
            if (!empty($options['subjectName'])) {
                $subjectName = true;
            }
        }

        // Attach all certificate nodes and any additional data
        foreach ($certs as $X509Cert) {
            if ($issuerSerial || $subjectName) {
                if ($certData = openssl_x509_parse("-----BEGIN CERTIFICATE-----\n" . chunk_split($X509Cert, 64, "\n") . "-----END CERTIFICATE-----\n")) {
                    if ($subjectName && !empty($certData['subject'])) {
                        if (is_array($certData['subject'])) {
                            $parts = array();
                            foreach ($certData['subject'] as $key => $value) {
                                if (is_array($value)) {
                                    foreach ($value as $valueElement) {
                                        array_unshift($parts, "$key=$valueElement");
                                    }
                                } else {
                                    array_unshift($parts, "$key=$value");
                                }
                            }
                            $subjectNameValue = implode(',', $parts);
                        } else {
                            $subjectNameValue = $certData['subject'];
                        }
                        $x509SubjectNode = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx . 'X509SubjectName', $subjectNameValue);
                        $x509DataNode->appendChild($x509SubjectNode);
                    }
                    if ($issuerSerial && !empty($certData['issuer']) && !empty($certData['serialNumber'])) {
                        if (is_array($certData['issuer'])) {
                            $parts = array();
                            foreach ($certData['issuer'] as $key => $value) {
                                array_unshift($parts, "$key=$value");
                            }
                            $issuerName = implode(',', $parts);
                        } else {
                            $issuerName = $certData['issuer'];
                        }

                        $x509IssuerNode = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx . 'X509IssuerSerial');
                        $x509DataNode->appendChild($x509IssuerNode);

                        $x509Node = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx . 'X509IssuerName', $issuerName);
                        $x509IssuerNode->appendChild($x509Node);
                        $x509Node = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx . 'X509SerialNumber', $certData['serialNumber']);
                        $x509IssuerNode->appendChild($x509Node);
                    }
                }

            }
            $x509CertNode = $baseDoc->createElementNS(self::XMLDSIGNS, $dsig_pfx . 'X509Certificate', $X509Cert);
            $x509DataNode->appendChild($x509CertNode);
        }
    }

    public static function generateGUID($prefix = 'pfx')
    {
        $uuid = md5(uniqid(mt_rand(), true));
        return $prefix . substr($uuid, 0, 8) . "-" .
            substr($uuid, 8, 4) . "-" .
            substr($uuid, 12, 4) . "-" .
            substr($uuid, 16, 4) . "-" .
            substr($uuid, 20, 12);
    }

    private function addRefInternal($sinfoNode, $node, $arTransforms = null, $options = null)
    {
        $prefix = null;
        $prefix_ns = null;
        $id_name = 'Id';
        $overwrite_id = true;
        $force_uri = false;

        if (is_array($options)) {
            $prefix = empty($options['prefix']) ? null : $options['prefix'];
            $prefix_ns = empty($options['prefix_ns']) ? null : $options['prefix_ns'];
            $id_name = empty($options['id_name']) ? 'Id' : $options['id_name'];
            $overwrite_id = !isset($options['overwrite']) ? true : (bool)$options['overwrite'];
            $force_uri = !isset($options['force_uri']) ? false : (bool)$options['force_uri'];
        }

        $attname = $id_name;
        if (!empty($prefix)) {
            $attname = $prefix . ':' . $attname;
        }

        $refNode = $this->createNewSignNode('Reference');
        $sinfoNode->appendChild($refNode);

        if (!$node instanceof DOMDocument) {
            $uri = null;
            if (!$overwrite_id) {
                $uri = $prefix_ns ? $node->getAttributeNS($prefix_ns, $id_name) : $node->getAttribute($id_name);
            }
            if (empty($uri)) {
                $uri = self::generateGUID();
                $node->setAttributeNS($prefix_ns, $attname, $uri);
            }
            $refNode->setAttribute("URI", '#' . $uri);
        } elseif ($force_uri) {
            $refNode->setAttribute("URI", '');
        }

        $transNodes = $this->createNewSignNode('Transforms');
        $refNode->appendChild($transNodes);

        if (is_array($arTransforms)) {
            foreach ($arTransforms as $transform) {
                $transNode = $this->createNewSignNode('Transform');
                $transNodes->appendChild($transNode);
                if (is_array($transform) &&
                    (!empty($transform['http://www.w3.org/TR/1999/REC-xpath-19991116'])) &&
                    (!empty($transform['http://www.w3.org/TR/1999/REC-xpath-19991116']['query']))) {
                    $transNode->setAttribute('Algorithm', 'http://www.w3.org/TR/1999/REC-xpath-19991116');
                    $XPathNode = $this->createNewSignNode('XPath', $transform['http://www.w3.org/TR/1999/REC-xpath-19991116']['query']);
                    $transNode->appendChild($XPathNode);
                    if (!empty($transform['http://www.w3.org/TR/1999/REC-xpath-19991116']['namespaces'])) {
                        foreach ($transform['http://www.w3.org/TR/1999/REC-xpath-19991116']['namespaces'] as $prefix => $namespace) {
                            $XPathNode->setAttributeNS("http://www.w3.org/2000/xmlns/", "xmlns:$prefix", $namespace);
                        }
                    }
                } else {
                    $transNode->setAttribute('Algorithm', $transform);
                }
            }
        } elseif (!empty($this->canonicalMethod)) {
            $transNode = $this->createNewSignNode('Transform');
            $transNodes->appendChild($transNode);
            $transNode->setAttribute('Algorithm', $this->canonicalMethod);
        }

        $canonicalData = $this->processTransforms($refNode, $node);

        $digValue = hash('sha256', $canonicalData, true);
        $digValue = base64_encode($digValue);

        $digestMethod = $this->createNewSignNode('DigestMethod');
        $refNode->appendChild($digestMethod);
        $digestMethod->setAttribute('Algorithm', 'http://www.w3.org/2001/04/xmlenc#sha256');

        $digestValue = $this->createNewSignNode('DigestValue', $digValue);
        $refNode->appendChild($digestValue);
    }

    public function processTransforms($refNode, $objData, $includeCommentNodes = true)
    {
        $data = $objData;
        $xpath = new DOMXPath($refNode->ownerDocument);
        $xpath->registerNamespace('secdsig', self::XMLDSIGNS);
        $query = './secdsig:Transforms/secdsig:Transform';
        $nodelist = $xpath->query($query, $refNode);
        $canonicalMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
        $arXPath = null;
        $prefixList = null;
        foreach ($nodelist as $transform) {
            $algorithm = $transform->getAttribute("Algorithm");
            switch ($algorithm) {
                case 'http://www.w3.org/2001/10/xml-exc-c14n#':
                case 'http://www.w3.org/2001/10/xml-exc-c14n#WithComments':

                    if (!$includeCommentNodes) {
                        /* We remove comment nodes by forcing it to use a canonicalization
                         * without comments.
                         */
                        $canonicalMethod = 'http://www.w3.org/2001/10/xml-exc-c14n#';
                    } else {
                        $canonicalMethod = $algorithm;
                    }

                    $node = $transform->firstChild;
                    while ($node) {
                        if ($node->localName == 'InclusiveNamespaces') {
                            if ($pfx = $node->getAttribute('PrefixList')) {
                                $arpfx = array();
                                $pfxlist = explode(" ", $pfx);
                                foreach ($pfxlist as $pfx) {
                                    $val = trim($pfx);
                                    if (!empty($val)) {
                                        $arpfx[] = $val;
                                    }
                                }
                                if (count($arpfx) > 0) {
                                    $prefixList = $arpfx;
                                }
                            }
                            break;
                        }
                        $node = $node->nextSibling;
                    }
                    break;
                case 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315':
                case 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315#WithComments':
                    if (!$includeCommentNodes) {
                        /* We remove comment nodes by forcing it to use a canonicalization
                         * without comments.
                         */
                        $canonicalMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';
                    } else {
                        $canonicalMethod = $algorithm;
                    }

                    break;
                case 'http://www.w3.org/TR/1999/REC-xpath-19991116':
                    $node = $transform->firstChild;
                    while ($node) {
                        if ($node->localName == 'XPath') {
                            $arXPath = array();
                            $arXPath['query'] = '(.//. | .//@* | .//namespace::*)[' . $node->nodeValue . ']';
                            $arXPath['namespaces'] = array();
                            $nslist = $xpath->query('./namespace::*', $node);
                            foreach ($nslist as $nsnode) {
                                if ($nsnode->localName != "xml") {
                                    $arXPath['namespaces'][$nsnode->localName] = $nsnode->nodeValue;
                                }
                            }
                            break;
                        }
                        $node = $node->nextSibling;
                    }
                    break;
            }
        }
        if ($data instanceof DOMNode) {
            $data = $this->canonicalizeData($objData, $canonicalMethod, $arXPath, $prefixList);
        }
        return $data;
    }

    private function addReference($node, $arTransforms = null, $options = null)
    {
        if ($xpath = $this->getXPathObj()) {
            $query = "./secdsig:SignedInfo";
            $nodeset = $xpath->query($query, $this->sigNode);
            if ($sInfo = $nodeset->item(0)) {
                $this->addRefInternal($sInfo, $node, $arTransforms, $options);
            }
        }
    }

    private function setCanonicalMethod()
    {
        $this->canonicalMethod = 'http://www.w3.org/TR/2001/REC-xml-c14n-20010315';

        if ($xpath = $this->getXPathObj()) {
            $query = './' . $this->searchpfx . ':SignedInfo';
            $nodeset = $xpath->query($query, $this->sigNode);
            if ($sinfo = $nodeset->item(0)) {
                $query = './' . $this->searchpfx . 'CanonicalizationMethod';
                $nodeset = $xpath->query($query, $sinfo);
                if (!($canonNode = $nodeset->item(0))) {
                    $canonNode = $this->createNewSignNode('CanonicalizationMethod');
                    $sinfo->insertBefore($canonNode, $sinfo->firstChild);
                }
                $canonNode->setAttribute('Algorithm', $this->canonicalMethod);
            }
        }
    }

    private function create()
    {
        $sigdoc = new DOMDocument();
        $sigdoc->loadXML(self::BASE_TEMPLATE);
        $this->sigNode = $sigdoc->documentElement;
    }

    /**
     * @param string $xmlPath ruta del archivo xml
     * @return string
     * @throws FirmaException
     */
    public function firmarRuta(string $xmlPath): string
    {
        return $this->firmar(file_get_contents($xmlPath));
    }
}
