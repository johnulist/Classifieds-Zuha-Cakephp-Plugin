<div class="classifieds form">
	<?php echo $this->Form->create('Classifieds.Classified', array('type' => 'file')); ?>
	<div class="row-fluid">
		<div class="span4">
			<?php echo $this->Form->input('Classified.title', array('type' => 'text')); ?>
		</div>
		<div class="span4">
			<?php echo $this->Form->input('Classified.expire_date', array('label' => 'Expiration Date', 'type' => 'datetimepicker', 'class' => 'input-medium', 'value' => date('Y-m-d h:i:s', strtotime('+30 days')))); ?>
		</div>
		<div class="span4">
			<!-- This is breaking the categories tree below-->
			<?php echo CakePlugin::loaded('Media') ? $this->Element('Media.selector', array('media' => $this->request->data['Media'], 'multiple' => true, 'bootstap' => 2)) : null; ?>
		</div>
	</div>

	<div class="row-fluid">
		<?php echo $this->Form->input('Classified.description', array('type' => 'textarea')); ?>
	</div>

	<div class="row-fluid">
		<div class="span3">
			<?php echo $this->Form->input('Classified.price', array('type' => 'text')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('Classified.condition', array('type' => 'text')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('Classified.payment_terms', array('type' => 'text')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('Classified.shipping_terms', array('type' => 'text')); ?>
		</div>
	</div>

	<div class="row-fluid">
		<?php /*<div class="span3">
			<?php if (CakePlugin::loaded('Categories')) : ?>
				<?php //echo $this->Form->input('Category.Category', array('type' => 'radio', 'legend' => false, 'class' => 'input-medium', 'purchasable' => true, 'combine' => array('{n}.Category.id', '{n}.Category.name'), 'options' => $categories, 'limit' => 3)); ?>
			<?php endif; ?>
		</div> */ ?>
		
		<div class="span3">
			<?php echo $this->Form->input('Classified.city', array('type' => 'text')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('Classified.state', array('empty' => '- choose -', 'options' => states(), 'class' => 'input-medium')); ?>
		</div>
		<div class="span3">
			<?php echo $this->Form->input('Classified.zip', array('type' => 'text')); ?>
		</div>
	</div>
	
	<div class="accordion row-fluid" id="catTest"></div>
	<?php echo $this->Tree->generate($categories, array('model' => 'Category', 'alias' => 'item_text', 'class' => 'categoriesList', 'id' => 'categoriesList', 'element' => 'Categories/input', 'elementPlugin' => 'classifieds')); ?>
	
	<?php //echo $this->Form->input('Classified.weight', array('type' => 'text')); ?>
	<?php echo $this->Form->end('Save'); ?>
</div>


<style type="text/css">
.categoriesList {
	display: none;
}
.accordion-heading .accordion-toggle {
	display: inline-block;
}
</style>
<script type="text/javascript">
	$(document).ready(function() {
		$('ul').css('list-style-type', 'none');
		$('input[type="radio"]').removeAttr('checked');
		
		var inputs = {};
		var key;
		var parent = '';
		var name = '';
		var key = '';
		var parent = '';
		var group = '';
		var children = new Array();
		var siblings = new Array();
		var selector = '';
		var temp = new Array();
		var attr = '';
		var append = 0;

		// put our existing radio buttons into an object grouped by depth and parent
		$.each($('input[type=radio]'), function(index, value) {
			key = $(value).attr('data-depth');
			inputs[key] = new Array();
		});
		$.each($('input[type=radio]'), function(index, value) {
			key = $(value).attr('data-depth');
			parent = $(value).attr('data-parent') ? $(value).attr('data-parent') : 'parent';
			inputs[key][parent] = new Array();
		});		
		$.each($('input[type=radio]'), function(index, value) {
			key = $(value).attr('data-depth');
			parent = $(value).attr('data-parent') ? $(value).attr('data-parent') : 'parent';
			inputs[key][parent][index] = $(value).parent().html();
		});
		// end building the big multi-dimensional array
		
		// create the accordion
		$.each(inputs, function(depth, obj) {
			for (var key in obj) {
				var value = obj[key];
				$('#catTest').append('<div class="accordion-group"><div class="accordion-heading"><a class="accordion-toggle" data-toggle="collapse" data-parent="#catTest" href="#collapse-' + key + '">Category</a></div><div id="collapse-' + key + '" class="accordion-body collapse in"><div class="accordion-inner depth-' + key + '"></div></div></div>')
				if (value) {
					$('#catTest .accordion-inner.depth-' + key).append(value.join(''));
				}
			}
		});
		
		// give accordion blocks the proper label
		$.each($('#catTest input[type=radio]'), function(index, value) {
			parent = $(this).attr('data-parent');
			label = $('.accordion-inner input[value=' + parent + ']').next().text();
			$('a[href=#collapse-' + parent + ']').text(label);
		});
		
		
		// now handle the selecting
		$('.accordion-group').hide();
		$('.accordion-group:first-child').show();
		
		$('input[type="radio"]').change(function() {
						
			// what to hide
			depth = $(this).data('depth') + 2; // anything two levels up gets hidden when we make a change to a previously selected category
			$('input').filter( function() {
				return $(this).data('depth') > depth;
			}).removeAttr('checked').parent().parent().parent().hide().find('.accordion-heading span.label').remove();
			
			siblings = $(this).siblings(); 
			selector = 'input[data-parent=';
			$.each(siblings, function(index) {
				attr = $(this).attr('data-children');
				if (attr) {
					temp = attr.split(',');
					selector = append ? selector + ', input[data-parent=' + temp.join('], input[data-parent=') + ']' : selector + temp.join('], input[data-parent=') + ']';
				} else {
					selector = append ? selector + ', input[data-parent=full]' : selector + 'none]';
				}
				append = 1;
			});
			$(selector).removeAttr('checked').parent().parent().parent().hide().find('.accordion-heading span.label').remove(); // selector is a string of all the input[data-parent] to hide, eg. the non-selected radios inputs next to the one that is selected
			append = 0; // reset
			selector = ''; // reset
			
			
			// what to show
			attr = $(this).attr('data-children');
			children = attr ? attr.split(',') : null; // children of selected input
			selector = children ? 'input[data-parent=' + children.join('], input[data-parent=') + ']' : null;
			$(selector).parent().parent().parent().show();
			
			// shrink up the current box after selection
			var heading = $('a.accordion-toggle', $(this).parent().parent().parent()); // '#collapse-99999-9999-99999-99999
			$(heading.attr('href')).collapse('hide');
			
			// add a little text to show what was chosen
			heading.parent().html($(heading).clone().wrap('<p>').parent().html() + ' <span class="label label-info">' + $(this).next().text() + '</span>');
		});
	});
	
</script>


<?php
// set the contextual menu items
$this->set('context_menu', array('menus' => array(
    array(
		'heading' => 'Classifieds',
		'items' => array(
			$this->Html->link(__('Dashboard'), array('admin' => true, 'controller' => 'classifieds', 'action' => 'dashboard')),
			$this->Html->link(__('List'), array('controller' => 'classifieds', 'action' => 'index')),
			)
		),
	)));
