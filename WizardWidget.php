<?php
/**
 * @copyright Copyright &copy; A.F.Schuurman, 2015
 * @package yii2-wizardwidget
 * @version 1.0.0
 */
namespace mohjak\wizardwidget;

use yii;
use yii\base\Widget;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Widget for wizard widget
 *
 * @author A.F.Schuurman <andre.schuurman+yii2-wizardwidget@gmail.com>
 * @since 1.0
 * @author Mohammad Jaqmaqji <mohjak@gmail.com>
 * @since 1.3.0
 */
class WizardWidget extends Widget
{
    /**
     * @var string widget html id
     */
    public $id = 'wizard';

    /**
     * @var array default button configuration
     */
    public $default_buttons = [
        'prev' => ['title' => 'Previous', 'options' => [ 'type' => 'button' ]],
        'next' => ['title' => 'Next', 'options' => [ 'type' => 'button' ]],
        'save' => ['title' => 'Save', 'options' => [ 'type' => 'button' ]],
        'skip' => ['title' => 'Skip', 'options' => [ 'type' => 'button' ]],
    ];

    /**
     * @var array the wizard step definition
     */
    public $steps = [];

    /**
     * @var integer step id to start with
     */
    public $start_step = null;

    /**
     * @var string optional final complete step content
     */
    public $complete_content = '';

    public $campaign_name = '';

    /**
     * Main entry to execute the widget
     */
    public function run()
    {
        parent::run();
        WizardWidgetAsset::register($this->getView());

        // Wizard line calculation
        // $step_count = count($this->steps)+($this->complete_content?1:0);
        // $wizard_line_distribution = round(100 / $step_count); // Percentage
        // $wizard_line_width = round(100 - $wizard_line_distribution); // Percentage
        $wizard_line = '';

        $name_line = '';

        $tab_content = '';

        // Navigation tracker
        end($this->steps);
        $last_id = key($this->steps);

        $first = true;
        $class = '';
        $step_navigation_area = '';

        foreach ($this->steps as $id => $step) {

            // Current or fist step is active, next steps are inactive (previous steps are available)
            if ($id == $this->start_step or (is_null($this->start_step) && $class == '')) {
                $class = 'active';
            } elseif ($class == 'active') {
                $class = 'disabled';
            }

            // Add icons to the wizard line
            $wizard_line .= Html::tag(
                'li',
                Html::a($step['title'], '#step'.$id, [
                    'data-toggle' => 'tab',
                    'aria-controls' => 'step'.$id,
                    'role' => 'tab',
                    'title' => $step['title'],
                ]),
                array_merge(
                    [
                        'role' => 'presentation',
                        'class' => $class,
                        // 'style' => ['width' => $wizard_line_distribution.'%']
                    ],
                    isset($step['options']) ? $step['options'] : []
                )
            );

            $wizard_line .= '>';

            // Setup tab content
            $tab_content .= '<div class="main tab-pane '.$class.'" role="tabpanel" id="step'.$id.'">';

            // Setup navigation buttons
            $buttons = [];
            $button_id = "{$this->id}_step{$id}_";
            if (!$first) {
                // Show previous button except on first step
                $buttons[] = $this->navButton('prev', $step, $button_id);
            }
            if (array_key_exists('skippable', $step) and $step['skippable'] === true) {
                // Show skip button if specified
                $buttons[] = $this->navButton('skip', $step, $button_id);
            }
            if ($id == $last_id) {
                // Show save button on last step
                $buttons[] = $this->navButton('save', $step, $button_id);
            } else {
                // On all previous steps show next button
                $buttons[] = $this->navButton('next', $step, $button_id);
            }

            // Add buttons to tab content
            $step_navigation_area .= '<div class="step-navigation-area">';
            $step_navigation_area .= '<h1 class="step-description">'.$step["description"].'</h1>';
            $step_navigation_area .= Html::ul($buttons, ['class' => 'step-navigation', 'encode' => false]);
            $step_navigation_area .= '</div>';

            $tab_content .= $step_navigation_area;

            unset($step_navigation_area);

            $tab_content .= '<div class="step-content">';
            $tab_content .= $step['content'];
            $tab_content .= '</div>';

            // Finish tab
            $tab_content .= '</div>';

            $first = false;
        }

        // Add a completed step if specified
        if ($this->complete_content) {
            $class = 'disabled';

            // Check if completed tab is set as start_step
            if ($this->start_step == 'completed') {
                $class = 'active';
            }

            // Add completed icon to wizard line
            $wizard_line .= Html::tag(
                'li',
                Html::a($step['title'], '#complete', [
                    'data-toggle' => 'tab',
                    'aria-controls' => 'complete',
                    'role' => 'tab',
                    'title' => 'Complete',
                ]),
                [
                    'role' => 'presentation',
                    'class' => $class,
                    // 'style' => ['width' => $wizard_line_distribution.'%']
                ]
            );

            $tab_content .= '<div class="tab-pane '.$class.'" role="tabpanel" id="complete">'.$this->complete_content.'</div>';
        }

        // Start widget
        echo '<div class="wizard" id="'.$this->id.'">';

        // Render the steps line
        $step_line .= '<div class="wizard-inner">';
        $step_line .= '<header>';
        $step_line .= '<nav>';
        $step_line .= '<div class="logo">';
        $step_line .= Html::a(Html::img('/files/logo/logo.svg', ['alt' => 'Falcoon Logo']), Url::to('/', true));
        $step_line .= '</div>';

        echo $step_line;

        // Setup name line
        $name_line .= '<div class="campaign-name">';
        $name_line .= Html::tag('h2', $this->campaign_name, ['title' => $this->campaign_name]);
        $name_line .= '</div>';

        echo $name_line;

        echo '<ul class="nav nav-tabs steps" role="tablist">'.$wizard_line.'</ul>';

        $save_campaign .= '<div class="save-campaign">';
        $save_campaign .= Html::a('Save Campaign', '#save-campaign');
        $save_campaign .=  '</div>';

        echo $save_campaign;

        echo '</nav>';

        echo '</header>';
        echo '</div>';

        // Render the content tabs
        echo '<div class="tab-content">'.$tab_content.'</div>';

        // Finalize the content tabs
        echo '<div class="clearfix"></div>';

        // Finish widget
        echo '</div>';
    }

    /**
     * Generate navigation button
     *
     * @param string $button_type prev|skip|next\save
     * @param array $step step configuration
     * @param string $button_id
     *
     * @return string
     */
    protected function navButton($button_type, $step, $button_id)
    {
        // Setup a unique button id
        $options = ['id' => $button_id.$button_type];

        // Apply default button configuration if defined
        if (isset($this->default_buttons[$button_type]['options'])) {
            $options = array_merge($options, $this->default_buttons[$button_type]['options']);
        }

        // Apply step specific button configuration if defined
        if (isset($step['buttons'][$button_type]['options'])) {
            $options = array_merge($options, $step['buttons'][$button_type]['options']);
        }

        // Add navigation class
        if ($button_type == 'prev') {
            $options['class'] = $options['class'].' prev-step previous';
        } else {
            $options['class'] = $options['class'].' next-step next';
        }

        // Display button
        if (isset($step['buttons'][$button_type]['html'])) {
            return $step['buttons'][$button_type]['html'];
        } elseif (isset($step['buttons'][$button_type]['title'])) {
            return Html::button($step['buttons'][ $button_type ]['title'], $options);
        } else {
            return Html::button($this->default_buttons[ $button_type ]['title'], $options);
        }
    }
}
