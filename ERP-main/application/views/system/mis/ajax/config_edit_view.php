
                <div class="modal-body">
                    <input type="hidden" name="report_id" id="report_id" value="<?php echo $report_id ?>" />
                    <input type="hidden" name="config_row_id" id="config_row_id" value="<?php echo $config_row_id ?>" />
                    <input type="hidden" name="type" id="type" value="edit" />

                    <div class="col-sm-12">
                        <div class="zx-tab-content">
                            <div class="zx-tab-pane active" id="Tab-home-v">
                                
                                <hr>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Report Master Type</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('header_type1', array('' =>  'Please Select', '1' =>'Header', '2' =>'Total','3' =>'Group Total','4' =>'Group Group Total'), $result['header_type1'], 'class="form-control" id="header_type1" required'); ?>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Header Type</label><!--Status-->

                                    <div class="col-sm-4">
                                        <?php echo form_dropdown('header_type2', $type2_arr,$result['header_type2'], 'class="form-control" id="header_type2" required'); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Category ID</label><!--Status-->
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="cat_id" id="cat_id" value="<?php echo $result['cat_id'] ?>"   required />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Category Description</label><!--Status-->
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="cat_description" id="cat_description" value="<?php echo $result['cat_description'] ?>" required/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-2 control-label">Sort Order</label><!--Status-->
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" name="sort_order" id="sort_order" value="<?php echo $result['sort_order'] ?>"   required />
                                    </div>
                                </div>

                                <hr>


                                <div class="pull-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button><!--Close-->
                                    <button type="submit" class="btn btn-primary">Submit</button><!--Submit-->
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
            </form>
