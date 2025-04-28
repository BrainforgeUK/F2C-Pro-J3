<div class="f2c_button_bar"> {$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL} </div>
<div class="clearfix"></div>
<div class="row-fluid form-horizontal">
    <h1>{php} echo JText::_( 'Quest_Creator' );{/php}</h1>
    <div class="control-group f2c_field f2c_title">
        <div class="control-label f2c_field_label">{$F2C_TITLE_CAPTION}</div>
        <div class="controls f2c_field_value">{$F2C_TITLE}</div>
    </div>
    <div class="control-group f2c_field f2c_metadesc">
        <div class="control-label f2c_field_label">{$SHORT_DESC_CAPTION}</div>
        <div class="controls f2c_field_value">{$SHORT_DESC}</div>
    </div>
    <div class="control-group f2c_field f2c_language">
        <div class="control-label f2c_field_label">{$F2C_LANGUAGE_CAPTION}</div>
        <div class="controls f2c_field_value">{$F2C_LANGUAGE}</div>
    </div>
    <div class="control-group f2c_field f2c_state">
        <div class="control-label f2c_field_label">{$F2C_STATE_CAPTION}</div>
        <div class="controls f2c_field_value">{$F2C_STATE}</div>
    </div>

<!--

<div class="control-group f2c_field f2c_catid">
    <div class="control-label f2c_field_label">{$F2C_CATID_CAPTION}</div>
    <div class="controls f2c_field_value">{$F2C_CATID}</div>
</div>

-->

<div class="control-group">
    <div class="control-label f2c_field_label"><label id="jform_catid-lbl" for="jform_catid" class="hasTooltip required" title="&lt;strong&gt;Module&lt;/strong&gt;&lt;br /&gt;Category">
    {php}echo JText::_( 'QC_COURSE' );{/php}<span class="star">&#160;*</span></label></div>

{php}
$catid = '';
$app = JFactory::getApplication();
$article_id = $app->input->get('id');
if( $article_id != '' )
{
//echo 'article: ' . $article_id . '<br />';
$db = JFactory::getDbo(); 
$db->setQuery('select catid from #__f2c_form where id='.$article_id); 
$catid = $db->loadResult(); 
}
$_SESSION['catid'] = $catid;

{/php}
    <div class="controls f2c_field_value">
        <select id="jform_catid" name="jform[catid]" class="inputbox required span6" required aria-required="true">
            <option value="">- {php}echo JText::_( 'SELECT_A_COURSE' );{/php} -</option>

            <option {php}echo ($_SESSION['catid'] == '32') ? 'selected ' : '';{/php}value="32">{php}echo JText::_( 'CAT_NAME_01' );{/php}</option>

            <option {php}echo ($_SESSION['catid'] == '33') ? 'selected ' : '';{/php}value="33">{php}echo JText::_( 'CAT_NAME_02' );{/php}</option>

            <option {php}echo ($_SESSION['catid'] == '34') ? 'selected ' : '';{/php}value="34">{php}echo JText::_( 'CAT_NAME_03' );{/php}</option>

            <option {php}echo ($_SESSION['catid'] == '35') ? 'selected ' : '';{/php}value="35">{php}echo JText::_( 'CAT_NAME_04' );{/php}</option>

            <option {php}echo ($_SESSION['catid'] == '36') ? 'selected ' : '';{/php}value="36">{php}echo JText::_( 'CAT_NAME_05' );{/php}</option>

            <option {php}echo ($_SESSION['catid'] == '37') ? 'selected ' : '';{/php}value="37">{php}echo JText::_( 'CAT_NAME_06' );{/php}</option>

            <option {php}echo ($_SESSION['catid'] == '38') ? 'selected ' : '';{/php}value="38">{php}echo JText::_( 'CAT_NAME_07' );{/php}</option>

         
        </select>

    </div>

<p>&nbsp;</p>

    <!---New template from here---->
    <!-- START: Sliders -->
    <div class="rl_sliders nn_sliders accordion panel-group" id="set-rl_sliders-1" role="presentation"> <a id="rl_sliders-scrollto_1" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
        <div class="accordion-group panel rl_sliders-group nn_sliders-group active"> <a id="rl_sliders-scrollto_sa1" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="sa1"> <a href="#sa1" title="{php} echo JText::_( 'Slider_a1' ); {/php}" class="accordion-toggle rl_sliders-toggle nn_sliders-toggle rl_sliders-item-scroll nn_sliders-item-scroll" data-toggle="collapse" id="slider-sa1"
                    data-id="sa1" data-parent="#set-rl_sliders-1" aria-expanded="true">					<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner"> {php}echo JText::_( 'Slider_a1' );{/php}</span>				</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse in"
                role="region" aria-labelledby="slider-sa1" aria-hidden="false" id="sa1">
                <div class="accordion-inner panel-body">
                    <h2 class="rl_sliders-title nn_sliders-title">Introduction</h2>
                    <div class="control-group f2c_field f2c_INTRO_IMAGE">
                        <div class="control-label f2c_field_label">{$INTRO_IMAGE_CAPTION}<br>&nbsp;<br>Write a descriptive title and copyright information</div>
                        <div class="controls f2c_field_value">{$INTRO_IMAGE}</div>
                    </div>
                    <div class="control-group f2c_field f2c_INTRO_TEXT">
                        <div class="control-label f2c_field_label">{$INTRO_TEXT_CAPTION}</div>
                        <div class="controls f2c_field_value">{$INTRO_TEXT}</div>
                    </div>
                    <div class="f2c_button_bar">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>
                </div>
            </div>
        </div>
        <div class="accordion-group panel rl_sliders-group nn_sliders-group"> <a id="rl_sliders-scrollto_sa2" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="sa2"> <a href="#sa2" title="{php} echo JText::_( 'Slider_a2' ); {/php}" class="accordion-toggle rl_sliders-toggle  rl_sliders-item-scroll nn_sliders-item-scroll nn_sliders-toggle collapsed" data-toggle="collapse" id="slider-sa2" data-id="sa2"
                    data-parent="#set-rl_sliders-1" aria-expanded="false">					<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner"> {php} echo JText::_( 'Slider_a2' ); {/php}</span>				</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse" role="region"
                aria-labelledby="slider-sa2" aria-hidden="true" id="sa2">
                <div class="accordion-inner panel-body">
                                                <!-- <h2 class="rl_sliders-title nn_sliders-title">Learning Objectives</h2> -->
                </div>

                    <div class="control-group f2c_field f2c_LEARN_MORE_IMAGE">
                        <div class="control-label f2c_field_label">{$LEARN_MORE_IMAGE_CAPTION}<br>&nbsp;<br>Write a descriptive title and copyright information</div>
                        <div class="controls f2c_field_value">{$LEARN_MORE_IMAGE}</div>
                    </div>

                    <div class="control-group f2c_field f2c_INSTRUCIONS_TEXT">
                        <div class="control-label f2c_field_label">{$LEARNMORE_CAPTION}</div>
                        <div class="controls f2c_field_value">{$LEARNMORE_TEXT}</div>
                        {$LEARNMORE} 
                    </div>
<div class="f2c_button_bar">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>            
</div></div>


        <div class="accordion-group panel rl_sliders-group nn_sliders-group"> <a id="rl_sliders-scrollto_sa3" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="sa3"> <a href="#sa3" title="{php} echo JText::_( 'Slider_a3' ); {/php}" class="accordion-toggle rl_sliders-toggle nn_sliders-toggle  rl_sliders-item-scroll nn_sliders-item-scroll collapsed" data-toggle="collapse" id="slider-sa3" data-id="sa3" data-parent="#set-rl_sliders-1"
                    aria-expanded="false">						<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner"> {php} echo JText::_( 'Slider_a3' ); {/php}</span>					</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse" role="region" aria-labelledby="slider-sa3"
                aria-hidden="true" id="sa3">
                <div class="accordion-inner panel-body">

                    <div class="control-group f2c_field f2c_INSTRUCIONS_TEXT">
                        <div class="control-label f2c_field_label">{$EXERCISE_CAPTION}</div>
                        <div class="controls f2c_field_value">{$EXERCISE_TEXT}</div>

                            {$EXERCISE}
                        </div>
<div class="f2c_button_bar">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>            
			</div>
			</div>		
        </div>
        <div class="accordion-group panel rl_sliders-group nn_sliders-group"> <a id="rl_sliders-scrollto_sa4" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="sa4"> <a href="#sa4" title="{php} echo JText::_( 'Slider_a4' ); {/php}" class="accordion-toggle rl_sliders-toggle nn_sliders-toggle  rl_sliders-item-scroll nn_sliders-item-scroll collapsed" data-toggle="collapse" id="slider-sa4" data-id="sa4" data-parent="#set-rl_sliders-1"
                    aria-expanded="false">										<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner"> {php} echo JText::_( 'Slider_a4' ); {/php}</span>									</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse" role="region" aria-labelledby="slider-sa4"
                aria-hidden="true" id="sa4">
                <div class="accordion-inner panel-body">
                                                  <!--<h2 class="rl_sliders-title nn_sliders-title">Requirements - Can this be deleted</h2>-->
                    <div class="control-group f2c_field f2c_REQUIREMENTS_TEXT">

                        {$REQUIREMENTS}
                    </div>
						<div class="f2c_button_bar">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>
                </div>
            </div>
        </div>
        <div class="accordion-group panel rl_sliders-group nn_sliders-group"> <a id="rl_sliders-scrollto_notes-for-sa5" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="notes-for-sa5"> <a href="#sa5" title="{php} echo JText::_( 'Slider_a5' ); {/php}" class="accordion-toggle rl_sliders-toggle  rl_sliders-item-scroll nn_sliders-item-scroll nn_sliders-toggle rl_sliders-item-scroll nn_sliders-item-scroll collapsed" data-toggle="collapse" id="slider-sa5" data-id="sa5"
                    data-parent="#set-rl_sliders-1" aria-expanded="false">										<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner"> {php} echo JText::_( 'Slider_a5' ); {/php}</span>									</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse"
                role="region" aria-labelledby="slider-sa5" aria-hidden="true" id="sa5">
                <div class="accordion-inner panel-body">
                                                      <!--<h2 class="rl_sliders-title nn_sliders-title">Notes for Educators</h2>-->
                    {$NOTES}
<div class="f2c_button_bar">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>
					</div>
					
            </div>
        </div>
    </div>
    <!-- END: Sliders -->
    <h2>{php} echo JText::_( 'QC_Resources' ); {/php}</h2>
    <!-- START: Sliders -->
    <div class="rl_sliders nn_sliders accordion panel-group" id="set-rl_sliders-2" role="presentation"> <a id="rl_sliders-scrollto_2" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
        <div class="accordion-group panel rl_sliders-group nn_sliders-group"> <a id="rl_sliders-scrollto_sb1" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="sb1"> <a href="#sb1" title="{php} echo JText::_( 'Slider_b1' ); {/php}" class="accordion-toggle rl_sliders-toggle nn_sliders-toggle  rl_sliders-item-scroll nn_sliders-item-scroll collapsed" data-toggle="collapse" id="slider-sb1" data-id="sb1" data-parent="#set-rl_sliders-2"
                    aria-expanded="false">										<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner">{php} echo JText::_( 'Slider_b1' ); {/php}</span>									</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse" role="region" aria-labelledby="slider-sb1"
                aria-hidden="true" id="sb1">
                <div class="accordion-inner panel-body">
                    <h2 class="rl_sliders-title nn_sliders-title">Websites URLs</h2>
                    <div class="control-group f2c_field f2c_LINKS_1">
                        <div class="control-label f2c_field_label">{$LINKS_1_CAPTION}</div>
                        <div class="controls f2c_field_value">{$LINKS_1}</div>
                    </div>
                    <div class="control-group f2c_field f2c_LINKS_2">
                        <div class="control-label f2c_field_label">{$LINKS_2_CAPTION}</div>
                        <div class="controls f2c_field_value">{$LINKS_2}</div>
                    </div>
                    <div class="control-group f2c_field f2c_LINKS_3">
                        <div class="control-label f2c_field_label">{$LINKS_3_CAPTION}</div>
                        <div class="controls f2c_field_value">{$LINKS_3}</div>
                    </div>
                    <div class="control-group f2c_field f2c_LINKS_4">
                        <div class="control-label f2c_field_label">{$LINKS_4_CAPTION}</div>
                        <div class="controls f2c_field_value">{$LINKS_4}</div>
                    </div>
                    <div class="control-group f2c_field f2c_LINKS_5">
                        <div class="control-label f2c_field_label">{$LINKS_5_CAPTION}</div>
                        <div class="controls f2c_field_value">{$LINKS_5}</div>
                    </div>
					<div class="f2c_button_bar">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>
                </div>
            </div>
        </div>
        <div class="accordion-group panel rl_sliders-group nn_sliders-group"> <a id="rl_sliders-scrollto_sb2" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="sb2"> <a href="#sb2" title="{php} echo JText::_( 'Slider_b2' ); {/php}" class="accordion-toggle rl_sliders-toggle nn_sliders-toggle  rl_sliders-item-scroll nn_sliders-item-scroll collapsed" data-toggle="collapse" id="slider-sb2" data-id="sb2"
                    data-parent="#set-rl_sliders-2" aria-expanded="false">										<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner">{php} echo JText::_( 'Slider_b2' ); {/php} </span>									</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse" role="region"
                aria-labelledby="slider-sb2" aria-hidden="true" id="sb2">
                <div class="accordion-inner panel-body">
                    <h2 class="rl_sliders-title nn_sliders-title">Videos from youtube</h2></span>
                    </a>
                </div>
                <div class="accordion-body nn_sliders-body collapse" id="videos">
                    <div class="accordion-inner panel-body">
                    </div>
                    <div class="control-group f2c_field f2c_YOUTUBE_1">
                        <div class="control-label f2c_field_label">{$YOUTUBE_1_CAPTION}</div>
                        <div class="controls f2c_field_value">{$YOUTUBE_1}</div>
                    </div>
                    <div class="control-group f2c_field f2c_YOUTUBE_2">
                        <div class="control-label f2c_field_label">{$YOUTUBE_2_CAPTION}</div>
                        <div class="controls f2c_field_value">{$YOUTUBE_2}</div>
                    </div>
                    <div class="control-group f2c_field f2c_YOUTUBE_3">
                        <div class="control-label f2c_field_label">{$YOUTUBE_3_CAPTION}</div>
                        <div class="controls f2c_field_value">{$YOUTUBE_3}</div>
                    </div>
                    <div class="control-group f2c_field f2c_YOUTUBE_4">
                        <div class="control-label f2c_field_label">{$YOUTUBE_4_CAPTION}</div>
                        <div class="controls f2c_field_value">{$YOUTUBE_4}</div>
                    </div>
                    <div class="control-group f2c_field f2c_YOUTUBE_5">
                        <div class="control-label f2c_field_label">{$YOUTUBE_5_CAPTION}</div>
                        <div class="controls f2c_field_value">{$YOUTUBE_5}</div>
                    </div>
                    <div class="f2c_button_bar"> {$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL} </div>
                </div>
            </div>
        </div>
        <div class="accordion-group panel rl_sliders-group nn_sliders-group"> <a id="rl_sliders-scrollto_documents" class="anchor rl_sliders-scroll nn_sliders-scroll"></a>
            <div class="accordion-heading panel-heading" aria-controls="sb3"> <a href="#sb3" title="{php} echo JText::_( 'Slider_b3' ); {/php}" class="accordion-toggle rl_sliders-toggle nn_sliders-toggle  rl_sliders-item-scroll nn_sliders-item-scroll collapsed" data-toggle="collapse" id="slider-sb3" data-id="sb3" data-parent="#set-rl_sliders-2"
                    aria-expanded="false">								<span class="rl_sliders-toggle-inner nn_sliders-toggle-inner"> {php} echo JText::_( 'Slider_b3' ); {/php}</span>							</a> </div>
            <div class="accordion-body rl_sliders-body nn_sliders-body collapse" role="region" aria-labelledby="slider-sb3"
                aria-hidden="true" id="sb3">
                <div class="accordion-inner panel-body">
                    <h2 class="rl_sliders-title nn_sliders-title">Documents</h2>
                    <div class="control-group f2c_field f2c_DOC1_NAME">
                        <div class="control-label f2c_field_label">{$DOC1_NAME_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC1_NAME}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC1">
                        <div class="control-label f2c_field_label">{$DOC1_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC1}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC2_NAME">
                        <div class="control-label f2c_field_label">{$DOC2_NAME_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC2_NAME}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC2">
                        <div class="control-label f2c_field_label">{$DOC2_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC2}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC3_NAME">
                        <div class="control-label f2c_field_label">{$DOC3_NAME_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC3_NAME}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC3">
                        <div class="control-label f2c_field_label">{$DOC3_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC3}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC4_NAME">
                        <div class="control-label f2c_field_label">{$DOC4_NAME_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC4_NAME}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC4">
                        <div class="control-label f2c_field_label">{$DOC4_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC4}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC5_NAME">
                        <div class="control-label f2c_field_label">{$DOC5_NAME_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC5_NAME}</div>
                    </div>
                    <div class="control-group f2c_field f2c_DOC5">
                        <div class="control-label f2c_field_label">{$DOC5_CAPTION}</div>
                        <div class="controls f2c_field_value">{$DOC5}</div>
                    </div>
					<div class="f2c_button_bar">{$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Sliders -->
    <!--Slider codes-->
    <div class="f2c_button_bar"> {$F2C_BUTTON_APPLY}{$F2C_BUTTON_SAVE}{$F2C_BUTTON_SAVE_AND_NEW}{$F2C_BUTTON_SAVE_AS_COPY}{$F2C_BUTTON_CANCEL}</div>
</div>
<div class="clearfix"></div>