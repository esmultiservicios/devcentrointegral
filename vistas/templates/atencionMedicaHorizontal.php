<div class="container-fluid" id="atencionMedica" style="display: none;">
    <form class="FormularioAjax" id="formulario_atenciones" action="" method="POST" data-form="" autocomplete="off"
        enctype="multipart/form-data">
        <button class="btn btn-primary ml-2" type="submit" id="reg_atencion" form="formulario_atenciones">
            <i class="far fa-save fa-lg"></i> Registrar
        </button>
        <button class="btn btn-primary ml-2" type="submit" id="edi_atencion" form="formulario_atenciones">
            <i class="far fa-save fa-lg"></i> Registrar
        </button>
        <br /><br />
        <ul class=" nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home"
                    aria-selected="false">
                    <i class="fas fa-info-circle fa-lg"></i> Datos Generales
                </a>
            </li>
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#historia_clinica_tab" role="tab"
                    aria-controls="historia_clinica" aria-selected="false">
                    <i class="fas fa-book-medical fa-lg"></i> Historia Clínica
                </a>
            </li>
            <li class="nav-item waves-effect waves-light">
                <a class="nav-link" id="seguimiento-tab" data-toggle="tab" href="#seguimiento_tab" role="tab"
                    aria-controls="seguimiento_tab" aria-selected="true">
                    <i class="fas fa-stethoscope fa-lg"></i> Tratamiento
                </a>
            </li>
        </ul>

        <br>
        <div class="tab-content" id="myTabContent">
            <div class="form-row">
                <div class="col-md-12 mb-3">
                    <input type="hidden" id="agenda_id" name="agenda_id" class="form-control">
                    <input type="hidden" required readonly id="pacientes_id" name="pacientes_id" />
                    <div class="input-group mb-3">
                        <input type="text" required readonly id="pro" name="pro" class="form-control" />
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <div class="sb-nav-link-icon"></div><i class="fa fa-plus-square"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- INICIO TAB CONTENT-->
            <div class="tab-pane fade active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                <!-- INICIO TAB HOME-->
                <div class="form-row">
                    <div class="col-md-3 mb-3">
                        <label for="paciente_consulta">Paciente <span class="priority">*<span /></label>
                        <div class="input-group mb-3">
                            <select class="selectpicker" id="paciente_consulta" name="paciente_consulta"
                                data-live-search="true" title="Paciente" data-width="100%" data-size="7">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Fecha de Registro <span class="priority">*<span /></label>
                        <input type="date" id="fecha" name="fecha" value="<?php echo date ("Y-m-d");?>"
                            class="form-control" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Fecha Nacimiento <span class="priority">*<span /></label>
                        <input type="date" id="fecha_nac" name="fecha_nac" value="<?php echo date ("Y-m-d");?>"
                            class="form-control" />
                    </div>                    
                    <div class="col-md-3 mb-3">
                        <label for="edad">Edad</label>
                        <input type="text" id="edad" name="edad" readonly class="form-control" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="servicio_id">Consultorio <span class="priority">*<span /></label>
                        <div class="input-group mb-3">
                            <select class="selectpicker" id="servicio_id" name="servicio_id" data-live-search="true"
                                title="Consultorio" data-width="100%" data-size="7">
                            </select>
                        </div>
                    </div>                    
                    <div class="col-md-8 mb-3">
                        <label for="preclinica">Preclinica</label>
                        <input type="text" id="preclinica" name="preclinica" readonly class="form-control"/>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <label for="procedencia">Dirección</label>
                        <input type="text" name="procedencia" id="procedencia" placeholder="Dirección" readonly
                            class="form-control" />
                    </div>
                </div>
            </div><!-- FIN TAB HOME-->
            <div class="tab-pane fade" id="historia_clinica_tab" role="tabpanel" aria-labelledby="historia_clinica-tab">
                <!-- INICIO TAB HISTORIA CLINICA-->
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header text-white bg-info mb-3" align="center">
                                Antecedentes
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <textarea id="antecedentes" name="antecedentes" placeholder="Antecedentes"
                                        class="form-control" maxlength="3200" rows="8"></textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="btn btn-outline-success fas fa-microphone-alt"
                                                id="search_antecedentes_start"></i>
                                            <i class="btn btn-outline-success fas fa-microphone-slash"
                                                id="search_antecedentes_stop"></i>
                                        </span>
                                    </div>
                                </div>
                                <p id="charNum_antecedentes">3200 Caracteres</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header text-white bg-info mb-3" align="center">
                                Historia Clínica
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <textarea id="historia_clinica" name="historia_clinica"
                                        placeholder="Historia Clinica" class="form-control" maxlength="3200"
                                        rows="8"></textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="btn btn-outline-success fas fa-microphone-alt"
                                                id="search_historia_clinica_start"></i>
                                            <i class="btn btn-outline-success fas fa-microphone-slash"
                                                id="search_historia_clinica_stop"></i>
                                    </div>
                                </div>
                                <p id="charNum_historia_clinica">3200 Caracteres</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header text-white bg-info mb-3" align="center">
                                Examen Físico
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <textarea id="exame_fisico" name="exame_fisico" placeholder="Examen Físico"
                                        class="form-control" maxlength="3200" rows="8"></textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="btn btn-outline-success fas fa-microphone-alt"
                                                id="search_exame_fisico_start"></i>
                                            <i class="btn btn-outline-success fas fa-microphone-slash"
                                                id="search_exame_fisico_stop"></i>
                                    </div>
                                </div>
                                <p id="charNum_exame_fisico">3200 Caracteres</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header text-white bg-info mb-3" align="center">
                                Diagnostico
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <textarea id="diagnostico" name="diagnostico" placeholder="Diagnostico"
                                        class="form-control" maxlength="3200" rows="8"></textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="btn btn-outline-success fas fa-microphone-alt"
                                                id="search_diagnostico_start"></i>
                                            <i class="btn btn-outline-success fas fa-microphone-slash"
                                                id="search_diagnostico_stop"></i>
                                    </div>
                                </div>
                                <p id="charNum_diagnostico">3200 Caracteres</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- FIN TAB HISTORIA CLINICA-->
            <div class="tab-pane fade" id="seguimiento_tab" role="tabpanel" aria-labelledby="seguimiento-tab">
                <!-- INICIO TAB SEGUIMIENTO-->
                <div class="form-row">
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header text-white bg-info mb-3" align="center">
                                Seguimiento (Tratamiento)
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <textarea id="seguimiento" name="seguimiento" placeholder="Tratamiento"
                                        class="form-control" maxlength="3200" rows="8"></textarea>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="btn btn-outline-success fas fa-microphone-alt"
                                                id="search_seguimiento_start"></i>
                                            <i class="btn btn-outline-success fas fa-microphone-slash"
                                                id="search_seguimiento_stop"></i>
                                    </div>
                                </div>
                                <p id="charNum_seguimiento">3200 Caracteres</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-header text-white bg-info mb-3" align="center">
                                Historia Seguimiento (Tratamiento)
                            </div>
                            <div class="card-body">
                                <textarea id="seguimiento_read" name="seguimiento_read" readonly
                                    placeholder="Tratamiento" class="form-control" maxlength="500" rows="11"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- FIN TAB SEGUIMIENTO-->
        </div><!-- FIN TAB CONTENT-->
</div>
</form>
</div>