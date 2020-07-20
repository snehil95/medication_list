<?php

use app\helpers\UserHelper;
use yii\helpers\ArrayHelper;
use app\helpers\SurveyInfoHelper;
use app\helpers\search1;
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\User;
use app\models\Survey;
use yii\helpers\Url;
use app\models\search\UserSearch;
use kartik\date\DatePicker;
use yii\web\View;
use yii\widgets\ActiveForm;
use app\assets\AdminLTEAsset;

Html::csrfMetaTags();

$this->registerJsFile(Yii::getAlias('@web/../themes/adminlte/custom/js/medication_list.js'), ['depends' => [AdminLTEAsset::className()]]);




$this->title = Yii::t('app', 'Medication/Surgery');
$this->params['breadcrumbs'][] = $this->title;


$this->registerJs("

    window.history.pushState(null, '', window.location.href);        
    window.onpopstate = function() {
        window.history.pushState(null, '', window.location.href);
    };

    // for select2 library
    $('.select2').select2();

    $('#surveyinfo-associate_clinic_id').next('span').find('.select2-selection--single').css('height', '36px');
    $('#surveyinfo-associate_clinic_id').next('span').find('.select2-selection--single').css('padding', '4px 8px');
    $('#surveyinfo-associate_clinic_id').next('span').find('.select2-selection__arrow').css('top', '5px');

    $('#surveyinfo-associate_clinic_id').change(function(){

        var base_url = $('#base_url').val();
        var actionUrl = base_url+'/survey/get-clinic-info';
        var selected_clinic = $(this).val();

        if(selected_clinic != '')
        {
            $.ajax({
                url: actionUrl,
                type: 'POST',
                data:{
                    selected_clinic: selected_clinic
                },
                success: function(response) {

                    var clinic = JSON.parse(response);
                    $('#clinic_name_text').text(clinic.clinic_name);
                    $('#clinic_address_text').text(clinic.clinic_address);
                },
                error: function() {
                    console.log('error');
                }
            });

        } else {
            $('#clinic_name_text').text('');
            $('#clinic_address_text').text('');
        } 
    });

    var selectedType = $( '#surveyinfo-employment_type option:selected' ).val();

    if(selectedType == '1' || selectedType == '') {
        $('.field-surveyinfo-employment_value').hide();
    } else {
        $('.field-surveyinfo-employment_value').show();
    }

    $('#surveyinfo-employment_type').change(function () {
        
        $('#surveyinfo-employment_value').val('');

        var selectedType = $( '#surveyinfo-employment_type option:selected' ).val();

        if(selectedType == '1'){
            $('.field-surveyinfo-employment_value').hide();
        } else {
            $('.field-surveyinfo-employment_value').show();
        }
    });

    $('#surveyinfo-body_part').on('change', function(){
        
        $('#surveyinfo-body_part_text').val('');

        var body_part = $('#surveyinfo-body_part option:selected').val();

        if(body_part != 'other'){
            $('.field-surveyinfo-body_part_text').hide();
        } else {
            $('.field-surveyinfo-body_part_text').show();
        }

        var base_url = $('#base_url').val();
        var actionUrl = base_url+'/survey/get-surgery-options';

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data:{
                body_part: body_part
            },
            success: function(response) {
                $('#surveyinfo-surgery_type').html(response);
            },
            error: function() {
                console.log('error');
            }
        });
    });

    $('#surveyinfo-surgery_type').change(function () {
        
        $('#surveyinfo-surgery_type_direction').val('');
        $('.field-surveyinfo-surgery_type_direction').show();

        var surgery_type = $('#surveyinfo-surgery_type option:selected').val();

        var base_url = $('#base_url').val();
        var actionUrl = base_url+'/survey/get-surgery-subtype-options';

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data:{
                surgery_type: surgery_type
            },
            success: function(response) {

                if(response != '') {
                    $('#surveyinfo-surgery_subtype').html(response);
                    $('#surgery_subtype_col').show();
                    $('#surgery_subtype_col_bottom').show();
                } else {
                    $('#surveyinfo-surgery_subtype').html('');
                    $('#surgery_subtype_col').hide();
                    $('#surgery_subtype_col_bottom').hide();
                }
            },
            error: function() {
                console.log('error');
            }
        });
    });
        $('#surveyinfo-follow_up').change(function () {
        
        $('#surveyinfo-follow_up_text').val('');

        var follow_up = $('#surveyinfo-follow_up option:selected').val();

        if(follow_up != 'other'){
            $('.field-surveyinfo-follow_up_text').hide();
        } else {
            $('.field-surveyinfo-follow_up_text').show();
        }
    });

    $('#surveyinfo-surgery_status').change(function () {
        
        $('#surveyinfo-surgery_status_date').val('');

        var surgery_status = $('#surveyinfo-surgery_status option:selected').val();

        if(surgery_status != 'I have temporary work restrictions through'){
            $('.field-surveyinfo-surgery_status_date').hide();
        } else {

            $('.field-surveyinfo-surgery_status_date').show();
            var surgery_status_date = $('#surgery_status_date').val();
            var text = $('#put').val();

                if(surgery_status_date == ''){

                    $('#put').show();
                    return false;
                }
                else
                {
                    $('#put').hide();
                }    
            
        }

    });

    $('#ad_btn').click(function (){

        var surgery_date = $('#surgery_date').val();
        var text = $('#text').val();

        if(surgery_date == ''){

            $('#text').show();
            return false;
        }
        else
        {
            $('#text').hide();
        }       

    });
    
$('#surveyinfo-frequency').change(function () {
        
        $('#surveyinfo-frequency_text').val('');

        var frequency = $('#surveyinfo-frequency option:selected').val();

        if(frequency != 'other'){
            $('.field-surveyinfo-frequency_text').hide();
        } else {
            $('.field-surveyinfo-frequency_text').show();
        }
    });

$('#surveyinfo-sideefects').change(function () {
        
        $('#surveyinfo-sideefects_text').val('');

        var sideefects = $('#surveyinfo-sideefects option:selected').val();

        if(sideefects != 'other'){
            $('.field-surveyinfo-sideefects_text').hide();
        } else {
            $('.field-surveyinfo-sideefects_text').show();
        }
    });



    $('#surveyinfo-had_surgery input:radio').change(function() {

        if(this.value != '0') {
            $('.surgery_section_fields_row').show();
            $('.surgery_section_btn_row').show();
        } else {
            $('.surgery_section_fields_row').hide();
            $('.surgery_section_btn_row').hide();
        }

        reset_surgery_section();
    });
    $('#surveyinfo-currently_taking_medication input:radio').change(function() {

        if(this.value != '0') {
            $('.medication_section_fields_row').show();
            $('.medication_section_btn_row').show();
        } else {
            $('.medication_section_fields_row').hide();
            $('.medication_section_btn_row').hide();
        }
        reset_medication_section();
    });
    $('#add_buttons').click(function () {

        var medication= $('#myInput').val();
        var medication_text = $('#myInput').text();

        var frequency= $('#surveyinfo-frequency_type option:selected').val();
        var frequency_str = $('#surveyinfo-frequency_type option:selected').text();

        var sideefects= $('#surveyinfo-sideefects_type option:selected').val();
        var sideefects_str = $('#surveyinfo-sideefects_type option:selected').text();
        

        var text_string = '';

        if(medication != '')
        {
            if(medication != 'other') {
                text_string += medication;
            }

            text_string += ' medication: ';
        }

        if(frequency != '')
        {
                text_string += frequency_str;
                text_string+=',';
        }

        if(sideefects != '')
        {
                text_string += sideefects_str;
        }
        

        text_string += \"\\n\";

        var existing_comment = $('#surveyinfo-currently_taking_medication_comment').val();

        text_string = existing_comment+text_string;

        $('#surveyinfo-currently_taking_medication_comment').val(text_string);
        reset_medication_section();

    });
        $('#add_button').click(function () {

        var body_part = $('#surveyinfo-body_part option:selected').val();
        var body_part_str = $('#surveyinfo-body_part option:selected').text();
        var body_part_text = $('#surveyinfo-body_part_text').val();

        var surgery_type = $('#surveyinfo-surgery_type option:selected').val();
        var surgery_type_str = $('#surveyinfo-surgery_type option:selected').text();

        var surgery_subtype = $('#surveyinfo-surgery_subtype option:selected').val();
        var surgery_subtype_str = $('#surveyinfo-surgery_subtype option:selected').text();

        var surgery_type_direction = $('#surveyinfo-surgery_type_direction option:selected').val();
        var surgery_type_direction_str = $('#surveyinfo-surgery_type_direction option:selected').text();

        var surgery_date = $('#surgery_date').val();

        var follow_up = $('#surveyinfo-follow_up option:selected').val();
        var follow_up_str = $('#surveyinfo-follow_up option:selected').text();
        var follow_up_text = $('#surveyinfo-follow_up_text').val();

        var surgery_status = $('#surveyinfo-surgery_status option:selected').val();
        var surgery_status_str = $('#surveyinfo-surgery_status option:selected').text();
        var surgery_status_date = $('#surgery_status_date').val();

        var text_string = '';

        if(body_part != '')
        {
            if(body_part != 'other') {
                text_string += body_part_str;
            } else {
                text_string += body_part_text;
            }

            text_string += ' surgery: ';
        }

        if(surgery_type != '')
        {
            if(surgery_type != 'other') {
                text_string += surgery_type_str;
                text_string += ', ';
            } else {
                text_string += surgery_type_str;
                text_string += ', ';
            }

            if(surgery_type_direction != '') {
                text_string += surgery_type_direction_str;
                text_string += ', ';
            }

            if(surgery_subtype != '') {
                text_string += surgery_subtype_str;
                text_string += ', ';
            }
        }

        if(surgery_date != '')
        {
            text_string += surgery_date;
            text_string += ', ';
        }

        if(follow_up != '')
        {
            if(follow_up != 'other') {
                text_string += follow_up_str;
            } else {
                text_string += follow_up_text;
            }

            text_string += ', ';
        }

        if(surgery_status != '')
        {
            if(surgery_status != 'status_option_2') {
                text_string += surgery_status_str;
            } else {
                text_string += surgery_status_str;
                text_string += ' ';
                text_string += surgery_status_date;
            }
        }

        text_string += \"\\n\";

        var existing_comment = $('#surveyinfo-had_surgery_comment').val();

        text_string = existing_comment+text_string;

        $('#surveyinfo-had_surgery_comment').val(text_string);

        reset_surgery_section();
        

    });

", View::POS_READY);

$this->registerJs("

    function reset_surgery_section()
    {
        $('#surveyinfo-body_part').val('');

        $('#surveyinfo-surgery_type').val('');
        $('#surveyinfo-surgery_type_direction').val('');
        $('.field-surveyinfo-surgery_type_direction').hide();

        $('#surveyinfo-surgery_subtype').val('');
        $('#surgery_subtype_col').hide();
        $('#surgery_subtype_col_bottom').hide();

        $('#surgery_date').val('');

        $('#surveyinfo-follow_up').val('');
        $('#surveyinfo-follow_up_text').val('');
        $('.field-surveyinfo-follow_up_text').hide();

        $('#surveyinfo-surgery_status').val('');
        $('#surgery_status_date').val('');
        $('.field-surveyinfo-surgery_status_date').hide();
    }

", View::POS_END);
$this->registerJs("

    function reset_medication_section()
    {
        $('#myInput').val('');
        $('#surveyinfo-frequency_type').val('');
        $('#surveyinfo-sideefects_type').val('');
        
    }

", View::POS_END);
?>
<style type="text/css">
    .form-box
    {
        margin-top: 20px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box_title_hdr form_pg ">
              <h3 class="box-title"><?php echo $this->title; ?></h3>
              
            </div>
            <!-- /.box-header -->
            <div class="box-body overflow_scroll">
            <ol>
                <div class="col-md-12">
                  <form method="post" action="/manage/appointments/medication-form">
                    <div class="form-group field-surveyinfo-had_surgery required">
                        <label class="control-label" style="margin-top: 10px;"><li>Have you ever had surgery? If "yes," please list and explain below</li></label>
                    </div>
                </div>
                <div class="row surgery_section_fields_row" style="">
                <div class="col-md-2">
                    <div class="form-group field-surveyinfo-body_part required">
                        <label class="control-label" for="surveyinfo-body_part">Body Part</label>
                        <select id="surveyinfo-body_part" class="form-control" name="body_part" required>
                            <option value="">-- Select --</option>
                                                        <option value="brain">Brain</option>
                                                        <option value="heart">Heart</option>
                                                        <option value="lungs">Lungs</option>
                                                        <option value="chest_breast">Chest/Breast</option>
                                                        <option value="thyroid">Thyroid</option>
                                                        <option value="upper_limb">Upper Limb</option>
                                                        <option value="abdomen">Abdomen</option>
                                                        <option value="pelvis">Pelvis</option>
                                                        <option value="lower_limb">Lower Limb</option>
                                                        <option value="skin">Skin</option>
                                                        <option value="other">Other</option>
                                                    </select>
                        <div class="help-block"></div>
                    </div>
                    <div class="form-group field-surveyinfo-body_part_text required" style="display: none;">
                        <textarea id="surveyinfo-body_part_text" class="form-control" name="body_part_text" aria-invalid="false"></textarea>
                        <p style="color: red; display: none;" id="body_text">This field is required</p>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group field-surveyinfo-surgery_type required">
                        <label class="control-label" for="surveyinfo-surgery_type">Surgery</label>
                        <select id="surveyinfo-surgery_type" class="form-control" name="surgery_type" required>
                            <option value="">-- Select --</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                    <div class="form-group field-surveyinfo-surgery_type_direction required" style="display: none;">
                        <select id="surveyinfo-surgery_type_direction" class="form-control" name="surgery_type_direction" required>
                            <option value="">-- Select --</option>
                            <option value="left">Left</option>
                            <option value="right">Right</option>
                            <option value="left_and_right">Left &amp; Right</option>
                        </select>
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="col-md-2" style="display: none;" id="surgery_subtype_col">
                    <div class="form-group field-surveyinfo-surgery_subtype required">
                        <label class="control-label" for="surveyinfo-surgery_subtype">Sub Type</label>
                        <select id="surveyinfo-surgery_subtype" class="form-control" name="surgery_subtype">
                            <option value="">-- Select --</option>
                        </select>
                        <div class="help-block"></div>
                        <p style="color: red; display: none;" id="sub_text">This field is required</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group field-surveyinfo-surgery_date required">
                        <label class="control-label" for="surveyinfo-surgery_date">Date</label>
                        <?= DatePicker::widget([
                            'name' => 'surgery_date',
                            'id' => 'surgery_date',
                            'removeButton' => false,
                            'pluginOptions' => [
                                'format'         => 'M/yyyy',
                                'todayHighlight' => true,
                                'autoclose'      => true,
                                'startView'      => 'years',
                                'minViewMode'    => 'months',
                                'changeMonth'    => true,
                                'changeYear'     => true,
                            ]
                        ]); ?>
                        <p style="color: red; display: none;" id="text">This field is required</p>
                        <div class="help-block"></div>
                    </div>
                    <div class="form-group" style="display: none;" id="surgery_subtype_col_bottom">
                        
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group field-surveyinfo-follow_up required">
                        <label class="control-label" for="surveyinfo-follow_up">Follow Up</label>
                        <select id="surveyinfo-follow_up" class="form-control" name="follow_up" required>
                            <option value="">-- Select --</option>
                                                        <option value="I have regular follow up">I have regular follow up</option>
                                                        <option value="My doctor does not require follow up">My doctor does not require follow up</option>
                                                        <option value="other">Other</option>
                                                    </select>
                        <div class="help-block"></div>
                    </div>
                    <div class="form-group field-surveyinfo-follow_up_text required" style="display: none;">
                        <textarea id="surveyinfo-follow_up_text" class="form-control" name="follow_up_text" aria-invalid="false"></textarea>
                        <div class="help-block"></div>
                        <p style="color: red; display: none;" id="follow_text">This field is required</p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group field-surveyinfo-surgery_status required">
                        <label class="control-label" for="surveyinfo-surgery_status">Status</label>
                        <select id="surveyinfo-surgery_status" class="form-control" name="surgery_status" required>
                            <option value="">-- Select --</option>
                                                        <option value="I do not have work restrictions or limitations due to the surgery and feel that I am safe to operate a commercial vehicle and perform my job">I do not have work restrictions or limitations due to the surgery and feel that I am safe to operate a commercial vehicle and perform my job</option>
                                                        <option value="I have temporary work restrictions through">I have temporary work restrictions through</option>
                                                        <option value="I have permanent work restrictions">I have permanent work restrictions</option>
                                                    </select>
                        <div class="help-block"></div>
                    </div>
                    <div class="form-group field-surveyinfo-surgery_status_date required" style="display: none;">
                        <?= DatePicker::widget([
                            'name' => 'surgery_status_date',
                            'id' => 'surgery_status_date',
                            'removeButton' => false,
                            'pluginOptions' => [
                                'format'         => 'mm/dd/yyyy',
                                'todayHighlight' => true,
                                'autoclose'      => true,
                                'startView'      => 'days',
                                'changeMonth'    => true,
                                'changeYear'     => true,
                            ]
                        ]); ?>
                        <div class="help-block"></div>
                        <p style="color: red; display: none;" id="put">This field is required</p>
                    </div>
                </div>
            </div>
            <div class="row surgery_section_btn_row" style="">
                <div class="col-sm-12">
                    <div class="form-group">
                        <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                        <input type="submit" name="submit_surgery" id="ad_btn" class="btn btn-xs btn-primary btn-gradient text-uppercase"></input>
                    </div>
                </div>
            </div>
        </form>
        <!-- Medication form-->
        
            <div class="col-md-12">
                <form method="post" action="/manage/appointments/medication-form">
                <div class="form-group field-surveyinfo-currently_taking_medication required">
                  <label class="control-label"><li>Are you currently taking medications (prescription, over-the-counter, herbal remedies, diet supplements)? If "yes," please describe below.</li></label>
                </div>
            </div>
            <div class="row medication_section_fields_row" style="">
                <div class="col-md-2">
                    <div class="form-group field-surveyinfo-medication required">
                        <label class="control-label" for="surveyinfo-medication">medication</label>
                        
  <div class="autocomplete" style="width:auto;">
    <input id="myInput" type="text" name="medication" placeholder="Medication" required>
  </div>



    <div class="help-block"></div>
</div>
<div class="form-group field-surveyinfo-medication_text required" style="display: none;">
    <textarea id="surveyinfo-medication_text" class="form-control" name="medication_text" aria-invalid="false"></textarea>
    <div class="help-block"></div>
</div>
</div>
<div class="col-md-2">
<div class="form-group field-surveyinfo-frequency_type required">
    <label class="control-label" for="surveyinfo-frequency_type">Frequency</label>
    <select id="surveyinfo-frequency_type" class="form-control" name="frequency_type" required>
        <option value="">-- Select --</option>
                                    <option value="once a day">once a day</option>
                                    <option value="twice a day">twice a day</option>
                                    <option value="three times a day">three times a day</option>
                                    <option value="four times a day">three times a day</option>
                                    <option value="before bed">before bed</option>
                                    <option value="five times a day">five times a day</option>
                                    <option value="every four hours">every four hours</option>
                                    <option value="every six hours">every six hours</option>
                                    <option value="every other day">every other day</option>
                                    <option value="as needed">as needed</option>
                                    <option value="before meals">before meals</option>
                                    <option value="after meals">after meals</option>
        
    </select>
    <div class="help-block"></div>
</div>
</div>
<div class="col-md-2">
<div class="form-group field-surveyinfo-sideefects_type required">
    <label class="control-label" for="surveyinfo-sideefects_type">Side effect</label>
    <select id="surveyinfo-sideefects_type" class="form-control" name="sideefects_type" required>
        <option value="">-- Select --</option>
                                    <option value="no side effects">no side effects</option>
                                    <option value="abnormal heart rhythms">abnormal heart rhythms</option>
                                    <option value="anxiety">anxiety</option>
                                    <option value="bruising">bruising</option>
                                    <option value="constipation">constipation</option>
                                    <option value="depression">depression</option>
                                    <option value="dizziness">dizziness</option>
                                    <option value="drowsiness">drowsiness</option>
                                    <option value="elevated blood pressure">elevated blood pressure</option>
                                    <option value="headache">headache</option>
                                    <option value="insomnia">insomnia</option>
                                    <option value="nausea">nausea</option>
                                    <option value="upset stomach">upset stomach</option>
                                    <option value="vomiting">vomiting</option>
                                    <option value="rash">rash</option>
                                    <option value="weight gain">weight gain</option>
        
    </select>
    <div class="help-block"></div>
</div>
</div>

<div class="row medication_section_btn_row" style="">
<div class="col-sm-12">
<div class="form-group">
    <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
    <input type="submit" name="submit_medication" id="" class="btn btn-xs btn-primary btn-gradient text-uppercase"></input>
    <?php if(!empty($surveysData)) { 

         foreach($surveysData as $survey){

            if ($survey->isFinished()!=NULL) {
                $link = Url::to(['/survey/summary', 'id' => $survey->id]);
            } else {
                $lastAnsweredQuestion = $survey->lastUnansweredQuestion();
                $link = Url::to([
                            '/survey/update',
                            'id'          => $survey->id,
                            'question_id' => $lastAnsweredQuestion->id
                        ]);
            }
           
           if($survey['visit_status'] == 'started_5875'){
          ?>

    <span><a style="margin-left: 76%;" href= "<?php echo $link; ?>" name="" id="" class="btn btn-xs btn-primary btn-gradient text-uppercase">Back to Survey</a></span><?php break; } } } ?>
</div>
</div>
</div>
</div>
</div>
</ol>
</form>
<!-- /.box-body -->
</div>
<!-- /.box -->

</div>
<!-- /.col -->
</div>
      
      <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
          <div class="box">
            <div class="box_title_hdr ">
              <h3 class="box-title">Surgery Data</h3>
              
            </div>
            <!-- /.box-header -->
            <div class="box-body" style="overflow-y: scroll; max-height: 300px;">

              <?php if(!empty($surgery_data)) { ?>

              <table id="appointmentslist" class="table table-bordered dataTable table-hover">
                <thead>
                <tr>
                  <th>Surgery Details</th>
                  <th>Delete</th>                  
                </tr>
                </thead>
                <tbody>
                  <?php 
                    foreach($surgery_data as $surgery) {
                        
                  ?>                  
                    <td><?php echo $surgery['had_surgery_comment']; ?></td>
                    <td><?= Html::a('Delete',['appointments/drop-surgery?id='.$surgery['id']])?></td>
                                        
                  </tr>
                  <?php } ?>
                </tbody>
              </table>

              <?php } else { ?>

                <div class="vital_mat_row">
                    <div class='rvw_doc_lft'>
                        <h5>No data available.</h5>
                    </div>
                </div>

              <?php } ?>

            </div>

            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          
        </div>
        <!-- /.col -->

      <!-- medication -->

        <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
          <div class="box">
            <div class=" box_title_hdr">
              <h3 class="box-title">Medication Data</h3>
              
            </div>
            <!-- /.box-header -->

            <div class="box-body" style="overflow-y: scroll; max-height: 300px;">

              <?php if(!empty($medication_data)) { ?>

              <table id="appointmentslist" class="table table-bordered dataTable table-hover">
                <thead>
                <tr>
                  <th>Medication Details</th>
                  <th>Delete</th>
                </tr>
                </thead>
                
                <tbody>
                  <?php 
                    foreach($medication_data as $medication) {
                        
                  ?>                  
                    
                    <td><?php echo $medication['currently_taking_medication']; ?></td>
                    <td><?= Html::a('Delete',['appointments/drop-medication?id='.$medication['id']])?></td>
                    
                  </tr>
                  <?php } ?>
                </tbody>
              </table>

              <?php } else { ?>

                <div class="vital_mat_row">
                    <div class='rvw_doc_lft'>
                        <h5>No data available.</h5>
                    </div>
                </div>

              <?php } ?>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
          
        </div>
        <!-- /.col -->
      </div>
      
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

</div>
<!-- /.admin_content -->


    
<style type="text/css">

.sm_btn{

        background-color: white;
        color: black;
        border-color: black;
        border-style: solid;
    }

.med_btn{
    float:right;
    background-color: #001066;
    color: white;
    margin-top: 10px;
    margin-bottom: 10px;
}
.medication_form{
    width: 100%;
    margin-left: 10px;
    border-top: 2px;
    margin-top: 10px;

    }
  .google_map_box{
    padding: 20px;
    display: block;
    width: 100%;
  }
  td {
      text-align: center;
  }
  .appointments_search_box{
    position: relative;
  }
  .appointments_search_box input{
    border: 1px solid #ccc;
      border-radius: 50px;
      padding: 0 10px; 
      color: #000;
      height: 35px;
  }
  .appointments_search_box span{
    position: absolute;
      top: 0;
      height: 35px;
      display: inline-block;
      right: 36px;
      padding: 0;
  }
  .appointments_search_box .input-group-btn button{
    border-radius: 0 50px 50px 0;
    background: transparent;
  }
  .dataTables_filter {
       display: none;
  }
  .content-wrapper .content .col-xs-12 .box-header{
      display: flex;
      align-items: center;
      justify-content: space-between;
  }
  .box-header:before, .box-body:before, .box-footer:before, .box-header:after, .box-body:after, .box-footer:after{
    display: none;
  }
</style>
<style>
* {
  box-sizing: border-box;
}

body {
  font: 16px Arial;  
}

/*the container must be positioned relative:*/
.autocomplete {
  position: relative;
  display: inline-block;}

input {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}

input[type=text] {
  background-color: #f1f1f1;
  width: 100%;
}

input[type=submit] {
  color: #fff;
  cursor: pointer;
  border-color: #fff;
  border-width: 1.5px;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
@media (min-width: 992px){
.que {
    width: 100%;
    margin-top: 6%;
}}

@media (max-width: 1024px){
.que {
    width: 100%;
    margin-top: 15%;
}}
.autocomplete-items {
    position: absolute;
    border: 1px solid #d4d4d4;
    border-bottom: none;
    border-top: none;
    z-index: 99;
    top: 100%;
    left: 0;
    right: 0;
    width: 132px;
    overflow-y: scroll;
    height: 183px;
    overflow-x: hidden;
}
</style>

