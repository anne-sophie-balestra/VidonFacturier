<!--Modal de modification de la prestation générale-->
<div class="modal fade" id="mod_updatePrestationGen" role="dialog" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modifier la prestation générale</h4>
            </div>
            <div class="modal-body">          
                <!--Creation d'un formulaire avec la validation Bootstrap-->
                <form id="formUpdatePrestationGen" action="index.php?action=updatePrestationGen" method="post" role="form" data-toggle="validator">                   
                    <div class="form-group">
                        <label class="control-label" for="operation">Opération :</label>
                            <select name="operation" id="operation" required class="form-control select2">
                            <?php //On affiche toutes les operations comme des options du select
                            $result_ope->execute();
                            foreach($result_ope->fetchAll(PDO::FETCH_OBJ) as $ope) { ?>
                                <option value="<?php echo $ope->t_ope_id; ?>" <?php if($ope->t_ope_id == $presta->pres_rf_typ_operation) { echo "selected"; } ?>><?php echo $ope->t_ope_libelle; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="type_dossier">Type de dossier :</label><br />
                        <!--On affiche les differents types de dossier possibles-->
                        <select name="type_dossier" id="type_dossier" required class="form-control select2">
                        <?php // On affiche les types de dossier disponibles 
                        $result_t_dos->execute();
                        foreach($result_t_dos->fetchAll(PDO::FETCH_OBJ) as $t_dos) { ?>
                            <option value="<?php echo $t_dos->t_dos_id; ?>" <?php if($t_dos->t_dos_id == $presta->pres_rf_typ_dossier) { echo "selected"; } ?>><?php echo $t_dos->t_dos_type; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="nom_code">Code :</label>
                        <!--On affiche les codes de nomenclature dans le select--> 
                        <select name="nom_code" id="nom_code" required class="form-control select2">
                        <?php foreach($result_nom->fetchAll(PDO::FETCH_OBJ) as $nom) { ?>
                            <option value="<?php echo $nom->nom_id; ?>" <?php if($nom->nom_id == $presta->pres_rf_nom) { echo "selected"; } ?>><?php echo $nom->nom_code; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="pays">Pays :</label>
                        <!--On affiche les pays en les groupant par regions-->
                        <select name="pays" id="pays" required class="form-control select2">
                        <?php foreach($result_pays_reg->fetchAll(PDO::FETCH_OBJ) as $pays_reg) { ?>
                            <optgroup label="<?php echo $pays_reg->pay_region; ?>">
                                <?php $stmt_pays = "SELECT pay_id, pay_nom FROM pays WHERE pay_region = '" . $pays_reg->pay_region . "' ORDER BY pay_nom";
                                $result_pays = $pdo->prepare($stmt_pays);
                                $result_pays->execute();
                                foreach($result_pays->fetchAll(PDO::FETCH_OBJ) as $pays) { ?>
                                    <option value="<?php echo $pays->pay_id; ?>" <?php if($pays->pay_id == $presta->pres_rf_pay) { echo "selected"; } ?>><?php echo $pays->pay_nom; ?></option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="prestation">Prestation :</label>
                        <!--on prend le nom general de la prestation, i.e. nom du modele-->
                        <input name="prestation" type="text" value="<?php echo $presta->pres_prestation; ?>" required class="form-control" id="prestation" maxlength="255" data-error="Veuillez entrer le nom de la prestation générale">
                        <div class="help-block with-errors"></div>
                    </div>
                    <!--On gere ici la repartition des consultants soit par un select, soit avec un slider (les deux sont liés)-->
                    <div class="form-group">
                        <label class="control-label" for="repartition">Répartition des consultants :</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <select id="pourcentage_select" class="form-inline" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('repartition').value=this.value;">
                                    <?php for($i=0; $i<=100; $i+=5) { ?>
                                        <option <?php if($presta->pres_repartition_cons == $i) { echo "selected"; } ?>><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                            </span>
                            <input name="repartition" id="repartition" value="<?php echo $presta->pres_repartition_cons; ?>" onchange="document.getElementById('pourcentage').innerHTML=this.value+'%';document.getElementById('pourcentage_select').value=this.value;" type="range" min="0" max="100" step="5" required class="form-control">
                            <span id="pourcentage" class="input-group-addon"><?php echo $presta->pres_repartition_cons; ?>%</span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">Modifier</button>
            </div>        
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->