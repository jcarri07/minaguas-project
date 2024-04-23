



<div class="container-fluid py-4">
    <div class="row">
        
        <div class="col-lg-12">
            <div class="card h-100 mb-3">
                <div class="card-header pb-0">
                    <h6 class="">Respaldo</h6>
                        <!--<div class="col-6 text-end">
                    <button class="btn btn-outline-primary btn-sm mb-0">View All</button>
                    </div>-->
                </div>

                <div class="card-body p-3 pb-0">
                    <div class="text-center mb-5 mt-5">
                        <!--<button type="button" onclick="alert('Te la creiste we 😂');" title="Descargar Respaldo" class="h6 py-1 btn mb-0 btn-outline-secondary btn-block border-2">-->
                        <button type="button" onclick="respaldo();" title="Descargar Respaldo" class="h6 py-1 btn mb-0 btn-outline-secondary btn-block border-2">
                            <i class="fa fa-hdd-o text-body opacity-10" style="font-size: 20px;"></i>
                            <span class="text-dark">
                                Descargar Respaldo
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function respaldo(){
        window.open('php/backup/modelos/backup-process.php', '_blank');
    }
</script>