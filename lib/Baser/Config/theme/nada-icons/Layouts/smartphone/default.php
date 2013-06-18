<?php
/**
 * デフォルトレイアウト
 */
?>
<?php $this->BcBaser->docType('html5') ?>
<html lang="ja">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=320, user-scalable=no">
<?php $this->BcBaser->title() ?>
<?php $this->BcBaser->metaDescription() ?>
<?php $this->BcBaser->metaKeywords() ?>
<?php $this->BcBaser->css(array('html5reset-1.6.1', 'smartphone/style')) ?>
<?php $this->BcBaser->js(array(
    'jquery-1.7.2.min',
	'smartphone/startup.js',
    'functions',
    'startup',
    'jquery.bxSlider.min',
    'jquery.easing.1.3',
    'nada-icons'
)) ?>
<?php $this->BcBaser->scripts() ?>
<?php $this->BcBaser->element('google_analytics', null, false, false) ?>
</head>
<body id="<?php $this->BcBaser->contentsName() ?>">
<div id="Page">

	<header>
		<div class="clearfix" id="BoxLogo">
			<div id="Logo"><h1><?php $this->BcBaser->link($this->BcBaser->siteConfig['name'],'/') ?></h1></div>
		</div>
		<div class="clearfix" id="global_menu">
    		<?php $this->BcBaser->element('global_menu') ?>
        </div>
	</header>
	
    <?php if($this->BcBaser->isTop()): ?>
    <div id="top-main">
        <div id="slider">
          <div><?php $this->BcBaser->img('slider/01.jpg'); ?></div>
          <div><?php $this->BcBaser->img('slider/02.jpg'); ?></div>
          <div><?php $this->BcBaser->img('slider/03.jpg'); ?></div>
          <div><?php $this->BcBaser->img('slider/04.jpg'); ?></div>
        </div>
    </div>
    <?php 
    /*
    *スライダーは色々設定ができるので参考にして下さい  http://zxcvbnmnbvcxz.com/demonstration/bxslide.html 
    *設定ファイルは js/nada-icons です
    */
    ?>
    <?php endif ?>

	<div id="ContentsBody" class="contents-body clearfix">
		<?php $this->BcBaser->flash() ?>
		<?php $this->BcBaser->content() ?>
		<?php $this->BcBaser->element('contents_navi') ?>
	</div>

	<div>
		<?php if(!empty($widgetArea)): ?>
		<?php $this->BcBaser->element('widget_area',array('no'=>$widgetArea)) ?>
		<?php endif ?>
	</div>
	
	<section id="ToTop">
	<a href="#Page">PAGE TOP</a>
	</section>
	
	<footer>
		<div class="clearfix" id="global_menu">
    		<?php $this->BcBaser->element('global_menu') ?>
        </div>

		<address>Copyright(C) 2008 - <?php echo date('Y')?> <br />baserCMS All rights Reserved.</address>
		<div class="banner">
		<?php $this->BcBaser->link($this->BcBaser->getImg('baser.power.gif', array('alt'=> 'baserCMS : Based Website Development Project', 'border'=> "0")),'http://basercms.net') ?>
		<?php $this->BcBaser->link($this->BcBaser->getImg('cake.power.gif', array('alt'=> 'CakePHP(tm) : Rapid Development Framework', 'border'=> "0")),'http://cakephp.jp') ?>
		</div>
	</footer>
	
</div>
<?php $this->BcBaser->func() ?>
</body>
</html>