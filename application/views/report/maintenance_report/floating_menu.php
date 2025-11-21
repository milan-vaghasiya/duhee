<div class="floatingButtonWrap">
	<div class="floatingButtonInner">
		<a href="#" class="floatingButton" datatip="Menu" flow="left"><i class="fa fa-plus"></i></a>
		<ul class="floatingMenu">
			<li><a href="<?=base_url('reports/maintenanceReport/machineReport')?>" class="bg-info">Machine Report</a></li>	
			<li><a href="<?=base_url('reports/maintenanceReport/partReplacement')?>" class="bg-danger">Part Replacement Report</a></li>			
			<li><a href="<?=base_url('reports/maintenanceReport/maintenanceLogReport')?>" class="bg-warning">Maintenance Log Book Report</a></li>			
			<li><a href="<?=base_url('reports/maintenanceReport/printPrevMaintenance')?>" class="bg-primary" target="_blank">Preventive Maintenance Plan</a></li>
		</ul>
	</div>
</div>
<script>
$(document).ready(function(){
	
	$(document).on('click','.floatingButton',
		function(e){
			e.preventDefault();
			$(this).toggleClass('open');
			if($(this).children('.fa').hasClass('fa-plus'))
			{
				$(this).children('.fa').removeClass('fa-plus');
				$(this).children('.fa').addClass('fa-times');
			} 
			else if ($(this).children('.fa').hasClass('fa-times')) 
			{
				$(this).children('.fa').removeClass('fa-times');
				$(this).children('.fa').addClass('fa-plus');
			}
			$('.floatingMenu').stop().slideToggle();
		}
	);
	$(this).on('click', function(e) {
		var container = $(".floatingButton");

		// if the target of the click isn't the container nor a descendant of the container
		if (!container.is(e.target) && $('.floatingButtonWrap').has(e.target).length === 0) 
		{
			if(container.hasClass('open'))
			{ 
				container.removeClass('open'); 
			}
			if (container.children('.fa').hasClass('fa-times')) 
			{
				container.children('.fa').removeClass('fa-times');
				container.children('.fa').addClass('fa-plus');
			}
			$('.floatingMenu').hide();
		}
	});
});
</script>