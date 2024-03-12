<!--begin::Table-->
{{-- <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users1">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th>ID</th>
            <th>Placa</th>
            <th >Cliente</th>
            <th >Fecha</th>
            <th >Monto</th>
            <th >Tipo</th>
            <th >Numero</th>
            <th >Estado</th>
            <th >Estado Siat</th>
            <th >Emision</th>
            <th >Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $pagos as  $p )
            <tr>
                <td class=" align-items-center">
                    <span class="text-info">
                        {{ $p->factura_id }}
                    </span>
                </td>
                <td>
                    @if ($p->vehiculo)
                    <a class="text-gray-800 text-hover-primary mb-1">{{ $p->vehiculo->placa }}</a>
                    @endif
                </td>
                <td>
                    @if ($p->vehiculo)
                    <a class="text-gray-800 text-hover-primary mb-1">{{ $p->vehiculo->cliente->nombres." ".$p->vehiculo->cliente->ap_paterno." ".$p->vehiculo->cliente->ap_materno }}</a>
                    @endif
                </td>
                <td>
                    <a class="text-gray-800 text-hover-primary mb-1">{{ $p->fecha }}</a>
                </td>
                <td>
                    <a class="text-gray-800 text-hover-primary mb-1">{{ $p->total }}</a>
                </td>
                <td>
                    @if($p->facturado === "Si")
                        <span class="badge badge-success badge-sm">Factura</span>
                    @else
                        <span class="badge badge-primary badge-sm">Recibo</span>
                    @endif
                </td>
                <td>
                    @if($p->facturado === "Si")
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $p->numero }}</a>
                    @else
                        <a class="text-gray-800 text-hover-primary mb-1">{{ $p->numero_recibo }}</a>
                    @endif
                </td>
                <td>
                    @if ($p->estado_factura === "Anulado")
                        <span class="badge badge-danger badge-sm">ANULADO</span>
                    @else
                        <span class="badge badge-success badge-sm">VIGENTE</span>
                    @endif
                </td>
                <td>
                    @php
                        if($p->codigo_descripcion == "VALIDADA"){
                            $text = "badge badge-success";
                        }elseif($p->codigo_descripcion == "PENDIENTE"){
                            $text = "badge badge-warning badge-sm";
                        }else{
                            $text = "badge badge-danger badge-sm";
                        }
                    @endphp
                    <span class="{{ $text }}" >{{ $p->codigo_descripcion }}</span>
                </td>
                <td>
                    @if ($p->tipo_factura === "online")
                        <span class="badge badge-success badge-sm" >Linea</span>
                    @elseif($p->tipo_factura === "offline")
                        <span class="badge badge-warning text-white badge-sm" >Fuera de Linea</span>
                    @endif
                </td>
                <td class="text-end">
                    @if ($p->estado_factura != 'Anulado')
                        <button class="btn btn-secondary btn-sm btn-icon" title="Ticked" onclick="generaTicked('{{ $p->factura_id }}', '{{ $p->id }}')"><i class="fa fa-file-circle-exclamation"></i></button>
                    @endif

                    @if($p->facturado === "Si")
                        <a  class="btn btn-primary btn-icon btn-sm"href="{{ url('factura/generaPdfFacturaNew', [$p->factura_id]) }}" target="_blank"><i class="fa fa-file-pdf"></i></a>
                        <a  class="btn btn-white btn-icon btn-sm"href="{{ url('factura/imprimeFactura', [$p->factura_id]) }}" target="_blank"><i class="fa fa-file-pdf"></i></a>
                        @if ($p->uso_cafc === "si")
                            <a href="https://siat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf={{ $p->cuf }}&numero={{ $p->numero_cafc }}&t=2" target="_blank" class="btn btn-dark btn-icon btn-sm"><i class="fa fa-file"></i></a>
                        @else
                            <a href="https://siat.impuestos.gob.bo/consulta/QR?nit=5427648016&cuf={{ $p->cuf }}&numero={{ $p->numero }}&t=2" target="_blank" class="btn btn-dark btn-icon btn-sm"><i class="fa fa-file"></i></a>
                        @endif
                        @if ($p->estado_factura != 'Anulado')
                            @if ($p->tipo_factura === "online")
                                @if ($p->productos_xml != null)
                                    @if(Auth::user()->isDelete())
                                        <button  class="btn btn-danger btn-icon btn-sm" type="button" onclick="modalAnular('{{ $p->factura_id }}')"><i class="fa fa-trash"></i></button>
                                    @endif
                                    <button class="btn btn-icon btn-sm btn-info" onclick="modalNuevaFacturaTramsferencia('{{ $p->factura_id }}')"><i class="fa fa-up-down"></i></button>
                                @else

                                @endif
                            @else
                                @if ($p->codigo_descripcion != 'VALIDADA' && $p->codigo_descripcion != 'PENDIENTE')
                                    <button class="btn btn-info btn-icon btn-sm" onclick="modalRecepcionFacuraContingenciaFueraLinea()"><i class="fa fa-upload" aria-hidden="true"></i></button>
                                @else
                                    @if(Auth::user()->isDelete())
                                        <button  class="btn btn-danger btn-icon btn-sm" type="button" onclick="modalAnular('{{ $p->factura_id }}')"><i class="fa fa-trash"></i></button>
                                    @endif
                                @endif
                            @endif
                        @endif
                    @else
                        @if($p->estado_factura != 'Anulado')
                            <a  class="btn btn-white btn-icon btn-sm"href="{{ url('factura/imprimeRecibo', [$p->factura_id]) }}" target="_blank"><i class="fa fa-file-pdf"></i></a>
                            @if(Auth::user()->isDelete())
                                <button  class="btn btn-danger btn-icon btn-sm" type="button" onclick="anularREcibo('{{ $p->factura_id }}')"><i class="fa fa-trash"></i></button>
                            @endif
                        @endif
                    @endif
                </td>
            </tr>
        @empty
            <h4 class="text-danger text-center">Sin registros</h4>
        @endforelse
    </tbody>
</table> --}}
<!--end::Table-->

<!--begin::Table-->
<table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
    <thead>
        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
            <th class="min-w-125px">Logo</th>
            <th class="min-w-125px">Nombre</th>
            <th class="min-w-125px">Nit</th>
            <th class="min-w-125px">Razon Social</th>
            <th class="min-w-125px">Ambiente</th>
            <th class="text-end min-w-100px">Actions</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-semibold">
        @forelse ( $empresas as $e)
            <tr>
                <td>{{ $e->logo }}</td>
                <td>{{ $e->nombre }}</td>
                <td>{{ $e->nit }}</td>
                <td>{{ $e->razon_social }}</td>
                <td>
                    @if ($e->codigo_ambiente === 2)
                        <span class="badge badge-warning">DESARROLLO</span>
                    @else
                        <span class="badge badge-success">PRODUCCION</span>
                    @endif
                </td>
                <td>
                    <a class="btn btn-sm btn-info btn-icon" title="Configuraciones de la Empresa" href="{{ url('empresa/detalle', [$e->id]) }}"><i class="fa fa-eye"></i></a>
                </td>
            </tr>
        @empty
            <h4 class="text-danger">No hay datos</h4>
        @endforelse
        {{-- <tr>
            <td class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                    <a href="#">
                        <div class="symbol-label">
                            <img src="{{ asset('assets/media/avatars/300-6.jpg') }}" alt="Emma Smith" class="w-100" />
                        </div>
                    </a>
                </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1">Emma Smith</a>
                    <span>smith@kpmg.com</span>
                </div>
                <!--begin::User details-->
            </td>
            <td>Administrator</td>
            <td>
                <div class="badge badge-light fw-bold">Yesterday</div>
            </td>
            <td></td>
            <td>15 Apr 2024, 11:05 am</td>
            <td class="text-end">
                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">Edit</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </td>
        </tr>
        <tr>
            <td class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                    <a href="#">
                        <div class="symbol-label fs-3 bg-light-danger text-danger">M</div>
                    </a>
                </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1">Melody Macy</a>
                    <span>melody@altbox.com</span>
                </div>
                <!--begin::User details-->
            </td>
            <td>Analyst</td>
            <td>
                <div class="badge badge-light fw-bold">20 mins ago</div>
            </td>
            <td>
                <div class="badge badge-light-success fw-bold">Enabled</div>
            </td>
            <td>15 Apr 2024, 5:20 pm</td>
            <td class="text-end">
                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">Edit</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </td>
        </tr>
        <tr>
            <td class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                    <a href="#">
                        <div class="symbol-label">
                            <img src="{{ asset('assets/media/avatars/300-1.jpg') }}" alt="Max Smith" class="w-100" />
                        </div>
                    </a>
                </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1">Max Smith</a>
                    <span>max@kt.com</span>
                </div>
                <!--begin::User details-->
            </td>
            <td>Developer</td>
            <td>
                <div class="badge badge-light fw-bold">3 days ago</div>
            </td>
            <td></td>
            <td>10 Nov 2024, 10:30 am</td>
            <td class="text-end">
                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">Edit</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </td>
        </tr>
        <tr>
            <td class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                    <a href="#">
                        <div class="symbol-label">
                            <img src="{{ asset('assets/media/avatars/300-5.jpg') }}" alt="Sean Bean" class="w-100" />
                        </div>
                    </a>
                </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1">Sean Bean</a>
                    <span>sean@dellito.com</span>
                </div>
                <!--begin::User details-->
            </td>
            <td>Support</td>
            <td>
                <div class="badge badge-light fw-bold">5 hours ago</div>
            </td>
            <td>
                <div class="badge badge-light-success fw-bold">Enabled</div>
            </td>
            <td>19 Aug 2024, 9:23 pm</td>
            <td class="text-end">
                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">Edit</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </td>
        </tr>
        <tr>
            <td class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                    <a href="#">
                        <div class="symbol-label">
                            <img src="{{ asset('assets/media/avatars/300-25.jpg') }}" alt="Brian Cox" class="w-100" />
                        </div>
                    </a>
                </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1">Brian Cox</a>
                    <span>brian@exchange.com</span>
                </div>
                <!--begin::User details-->
            </td>
            <td>Developer</td>
            <td>
                <div class="badge badge-light fw-bold">2 days ago</div>
            </td>
            <td>
                <div class="badge badge-light-success fw-bold">Enabled</div>
            </td>
            <td>15 Apr 2024, 11:30 am</td>
            <td class="text-end">
                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">Edit</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </td>
        </tr>
        <tr>
            <td class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                    <a href="#">
                        <div class="symbol-label fs-3 bg-light-warning text-warning">C</div>
                    </a>
                </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1">Mikaela Collins</a>
                    <span>mik@pex.com</span>
                </div>
                <!--begin::User details-->
            </td>
            <td>Administrator</td>
            <td>
                <div class="badge badge-light fw-bold">5 days ago</div>
            </td>
            <td></td>
            <td>25 Jul 2024, 10:30 am</td>
            <td class="text-end">
                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">Edit</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </td>
        </tr>
        <tr>
            <td class="d-flex align-items-center">
                <!--begin:: Avatar -->
                <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                    <a href="#">
                        <div class="symbol-label">
                            <img src="{{ asset('assets/media/avatars/300-9.jpg') }}" alt="Francis Mitcham" class="w-100" />
                        </div>
                    </a>
                </div>
                <!--end::Avatar-->
                <!--begin::User details-->
                <div class="d-flex flex-column">
                    <a href="#" class="text-gray-800 text-hover-primary mb-1">Francis Mitcham</a>
                    <span>f.mit@kpmg.com</span>
                </div>
                <!--begin::User details-->
            </td>
            <td>Trial</td>
            <td>
                <div class="badge badge-light fw-bold">3 weeks ago</div>
            </td>
            <td></td>
            <td>19 Aug 2024, 10:10 pm</td>
            <td class="text-end">
                <a href="#" class="btn btn-light btn-active-light-primary btn-flex btn-center btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4" data-kt-menu="true">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3">Edit</a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <a href="#" class="menu-link px-3" data-kt-users-table-filter="delete_row">Delete</a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </td>
        </tr> --}}
    </tbody>
</table>
<!--end::Table-->

<script>
    // $('#kt_table_users1').DataTable({
    //     lengthMenu: [ -1 ],
    //     ordering: true,
    //     initComplete: function() {
    //         this.api().order([0, "desc"]).draw();
    //     }
    // });

    $(document).ready(function() {
            $('#kt_table_users').DataTable({
                lengthMenu: [10, 25, 50, 100], // Opciones de longitud de página
                dom: '<"dt-head row"<"col-md-6"l><"col-md-6"f>><"clear">t<"dt-footer row"<"col-md-5"i><"col-md-7"p>>', // Use dom for basic layout
                language: {
                paginate: {
                    first : 'Primero',
                    last : 'Último',
                    next : 'Siguiente',
                    previous: 'Anterior'
                },
                search : 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros por página',
                info : 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                emptyTable: 'No hay datos disponibles'
                },
                order:[],
                //  searching: true,
                responsive: true
            });


        });
</script>
