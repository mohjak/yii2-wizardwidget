<?php
/**
 * @copyright Copyright &copy; A.F.Schuurman, 2015
 * @package yii2-wizardwidget
 * @version 1.0.0
 */
namespace mohjak\wizardwidget;

use yii\web\AssetBundle;

/**
 * Asset bundle for Wizard Widget
 *
 * @author A.F.Schuurman <andre.schuurman+yii2-wizardwidget@gmail.com>
 * @since 1.0
 * @author Mohammad Jaqmaqji <mohjak@gmail.com>
 * @since 1.3.0
 */
class WizardWidgetAsset extends AssetBundle
{
	public $sourcePath = '@mohjak/wizardwidget';
	public $depends = [
		'yii\web\YiiAsset',
		'yii\bootstrap\BootstrapPluginAsset'
	];
	public $css = [
		'css/wizardwidget.css',
	];
	public $js = [
		'js/wizardwidget.js'
	];
}
