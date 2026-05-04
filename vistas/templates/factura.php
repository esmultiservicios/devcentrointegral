<div class="table-responsive" id="facturacion" style="display: none;">	
    <form class="invoice-form FormularioAjax" id="formulario_facturacion" action="" method="POST" data-form="" enctype="multipart/form-data">

        <!-- BOTONES -->
        <div class="form-group row mb-3">
            <div class="col-12 text-left">
                <button class="btn btn-primary" type="submit" id="validar">
                    <i class="far fa-save fa-lg"></i> Cobrar
                </button>
                <button class="btn btn-secondary" type="submit" id="guardar">
                    <i class="far fa-save fa-lg"></i> Guardar
                </button>
                <button class="btn btn-secondary" type="submit" id="guardar1">
                    <i class="far fa-save fa-lg"></i> Guardar
                </button>
            </div>
        </div>

        <!-- FILA 1 -->
        <div class="form-group row mb-3">

            <label class="col-sm-1 col-form-label label-form">Paciente <span class="text-danger">*</label>
            <div class="col-sm-5">
                <select class="selectpicker" id="pacientes_id" name="pacientes_id"
                    required data-live-search="true" title="Paciente" data-width="100%" data-size="7">
                </select>
                <input type="hidden" id="facturas_id" name="facturas_id">
            </div>

            <label class="col-sm-1 col-form-label label-form">Fecha <span class="text-danger">*</label>
            <div class="col-sm-2">
                <input type="date" class="form-control"
                    value="<?php echo date('Y-m-d');?>" id="fecha" name="fecha" readonly required>
            </div>

            <label class="col-sm-1 col-form-label label-form">Profesional <span class="text-danger">*</label>
            <div class="col-sm-2">
                <select class="selectpicker" id="colaborador_id" name="colaborador_id"
                    required data-live-search="true" title="Profesional" data-width="100%" data-size="7">
                </select>
            </div>

        </div>

        <!-- FILA 2 -->
        <div class="form-group row mb-4">

            <label class="col-sm-1 col-form-label label-form">Servicio <span class="text-danger">*</label>
            <div class="col-sm-5">
                <select class="selectpicker" id="servicio_id" name="servicio_id"
                    required data-live-search="true" title="Servicio" data-width="100%" data-size="7">
                </select>
            </div>

            <!-- TIPO FACTURA -->
            <div class="col-sm-6">
                <label class="font-weight-bold d-block mb-2">Tipo de Factura</label>

                <div class="d-flex flex-wrap align-items-center">

                    <div class="form-check mr-4">
                        <input class="form-check-input" type="radio" name="facturas_activo"
                            id="factura_contado" value="1" checked>
                        <label class="form-check-label" for="factura_contado">
                            <i class="fas fa-money-bill-wave text-success"></i> Contado
                        </label>
                    </div>

                    <div class="form-check mr-4">
                        <input class="form-check-input" type="radio" name="facturas_activo"
                            id="factura_credito" value="2">
                        <label class="form-check-label" for="factura_credito">
                            <i class="fas fa-file-invoice-dollar text-warning"></i> Crédito
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="facturas_activo"
                            id="factura_proforma" value="3">
                        <label class="form-check-label" for="factura_proforma">
                            <i class="fas fa-file-alt text-primary"></i> Proforma
                        </label>
                    </div>

                </div>
            </div>

        </div>

        <!-- TABLA -->
        <div class="form-group row table-responsive-xl tableFixHead table table-hover">
            <div class="col-12">
                <table class="table table-bordered table-hover" id="invoiceItem">
                    <thead class="table-success text-center">
                        <tr>
                            <th width="2%"><input id="checkAll" type="checkbox"></th>
                            <th width="38%">Nombre Producto</th>
                            <th width="15%">Cantidad</th>
                            <th width="15%">Precio</th>
                            <th width="15%">Descuento</th>
                            <th width="15%">Total</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><input class="itemRow" type="checkbox"></td>

                            <td>
                                <input type="hidden" name="isv[]" id="isv_0">
                                <input type="hidden" name="valor_isv[]" id="valor_isv_0">
                                <input type="hidden" name="facturas_detalle_id[]" id="facturas_detalle_id_0">
                                <input type="hidden" name="productoID[]" id="productoID_0">

                                <input type="text" name="productName[]" id="productName_0"
                                    class="form-control producto" placeholder="Producto o Servicio">
                            </td>

                            <td><input type="number" name="quantity[]" id="quantity_0" class="form-control"></td>
                            <td><input type="number" name="price[]" id="price_0" class="form-control"></td>
                            <td><input type="number" name="discount[]" id="discount_0" class="form-control" readonly></td>
                            <td><input type="number" name="total[]" id="total_0" class="form-control total" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <hr class="line_table" />

        <!-- BOTONES TABLA -->
        <div class="form-group row mb-3">
            <div class="col-12">
                <button class="btn btn-success" id="addRows" type="button">
                    <i class="fas fa-plus fa-lg"></i> Agregar
                </button>
                <button class="btn btn-danger" id="removeRows" type="button">
                    <i class="fas fa-minus fa-lg"></i> Remover
                </button>
            </div>
        </div>

        <!-- NOTAS -->
        <div class="form-group row">
            <div class="col-12">
                <h5>Notas:</h5>
                <textarea class="form-control" rows="4" name="notes" id="notes"></textarea>
            </div>
        </div>

        <!-- FOOTER INTERNO (NO TOCAR) -->
        <div style="display: none;">
            <input type="number" id="subTotal" name="subTotal">
            <input type="number" id="taxAmount" name="taxAmount">
            <input type="number" id="taxDescuento" name="taxDescuento">
            <input type="number" id="totalAftertax" name="totalAftertax">
        </div>

        <!-- EMPRESA + ASEGURADORA EN UNA SOLA FILA -->
        <div class="form-group row">

            <label class="col-sm-1 col-form-label">Empresas</label>
            <div class="col-sm-5">
                <select class="selectpicker" id="fact_empresas_id" name="fact_empresas_id"
                    data-live-search="true" title="Empresas" data-width="100%">
                </select>
            </div>

            <label class="col-sm-1 col-form-label">Aseguradora</label>
            <div class="col-sm-5">
                <select class="selectpicker" id="aseguradora_id" name="aseguradora_id"
                    data-live-search="true" title="Aseguradora" data-width="100%">
                </select>
            </div>

        </div>

    </form>
</div>