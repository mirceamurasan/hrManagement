/**
* Erreur dans le flux AJAX
*/
function ajaxError( jqXHR, textStatus, errorThrown )
{
	console.log( jqXHR );
	console.log(textStatus);
	console.log(errorThrown);
}

/*
--------------------------------
	< TASKLOAD >
--------------------------------
*/
/**
* Affiche le retour d'erreur du form ou ajoute la ligne de la tache
*/
function addTasksloadsSuccess( data )
{
	if(data.status == 'error')
	{
		form_object = $(data.render);
		form_id = form_object.attr('id');
		$("#"+form_id).find('.providersidproviders').html(form_object.find('.providersidproviders').html());
		$("#"+form_id).find('.tasksloadsworkers').html(form_object.find('.tasksloadsworkers').html());
		$("#"+form_id).find('.tasksloadsvolume').html(form_object.find('.tasksloadsvolume').html());
		$("#"+form_id).find('.jobsidjobs').html(form_object.find('.jobsidjobs').html());
	} else {
		n = data.render.match(/job-for-task-[0-9]+/);
		id = n[0].replace('job-for-task-', '');
		$('#tasklist_' + id).append(data.render);
		prev_elem = $('.cycle-not-defined').prev();
		if(prev_elem.hasClass('odd'))
			$('.cycle-not-defined').addClass('even');
		else
			$('.cycle-not-defined').addClass('odd');
		$('.cycle-not-defined').removeClass('cycle-not-defined');
		form_id = $('#tasklist_' + id).find('form').attr('id');
		document.getElementById(form_id).reset();
	}
}
/**
* Executer en fin de parcours
*/
function addTasksloadsComplete( data, textStatus, jqXHR )
{

}
/**
* Ajoute une charge dans un projet
* @param EventListener jQuery
*/
function addTasksloads(event)
{
	event.preventDefault();

	$.ajax({
		url: $(this).attr('action'),
		type: 'POST',
		async: false,
		dataType: 'json',
		data: $(this).serialize()
		})
		.done(addTasksloadsSuccess)
		.fail(ajaxError)
		.always(addTasksloadsComplete);
}
/*
--------------------------------
	</ TASKLOAD >
--------------------------------
*/

/*
--------------------------------
	< TASK >
--------------------------------
*/

/**
* Affiche le retour d'erreur du form ou ajoute la ligne de la tache
*/
function addTaskSuccess( data )
{
	if(data.status == 'error')
	{
		form_object = $(data.render);
		form_id = form_object.attr('id');
		$("#"+form_id).find('.tasksname').html(form_object.find('.tasksname').html());
		$("#"+form_id).find('.tasksbeginat').html(form_object.find('.tasksbeginat').html());
		$("#"+form_id).find('.tasksendat').html(form_object.find('.tasksendat').html());
	} else {

		wrap = $('<div>');
		wrap.addClass('row-fluid');
		wrap.addClass('task-wrap');
		wrap.append( data.render );

		if( $('.fcast-projectdetail-tasklist').length )
			$(wrap).insertAfter($('.fcast-projectdetail-tasklist').last());
		else
			$(wrap).insertAfter($('.fcast-view-listtasks-title') );

		wrap.find('.editable-field').on('dblclick', editFiled);
		$('.tasksloadstypeform').off('submit').on('submit', addTasksloads);

		$.get( $('.taskstypeform').attr('data-load'), function( content ){
			$('.project-info').html( content );
			$('.project-info .editable-field').on('dblclick', editFiled);
		} );

		form_id = $('.taskstypeform').attr('id');
		document.getElementById(form_id).reset();
	}
}
/**
* Executer en fin de parcours
*/
function addTaskComplete( data, textStatus, jqXHR )
{

}
/**
* Ajoute une tache dans un projet
* @param EventListener jQuery
*/
function addTasks(event)
{
	event.preventDefault();

	$.ajax({
		url: $(this).attr('action'),
		type: 'POST',
		async: false,
		dataType: 'json',
		data: $(this).serialize()
		})
		.done(addTaskSuccess)
		.fail(ajaxError)
		.always(addTaskComplete);
}

/*
--------------------------------
	</ TASK >
--------------------------------
*/


/**
* affiche le retour de la fonction @FullSix\ProjectForecastBundle\Controller\ProjectsController\editInPlaceAction
* ne pas oublier d'ajouter les getter dans l'entité
* @param string json data
*/
function editInPlaceSuccess( data ) {
	if(data.status == 'error')
	{
		form_object = $(data.render);
		$(this).find('.field-to-change').html(form_object.find('.field-to-change').html());
	} else {
		form_object = $('#' + data.form_name + '_' + data.item_id + '_' + data.field_name + '_EIP');

		form_object.parent().on('dblclick', editFiled);;
		form_object.parent().find('span').text(data.render).show();
		form_object.remove();

        for( key in data.project)
        {
            console.log(key, data.project[key]);
            if($('.project-detail').find('.' + key).length)
                $('.project-detail').find('.' + key).text(data.project[key]);

            if(key == 'interval_date')
                $('progress').attr('max', data.project[key]);
            if(key == 'since_begining')
                $('progress').attr('value', data.project[key]);
        }

	}
}

function editInPlaceComplete(){

}

/**
* Affiche le formulaire
* @param EventListener event
*
*/
function updateEditInPlace( event )
{
	event.preventDefault();
	form_element = $(this).closest('form');

	$.ajax({
		url: form_element.attr('action'),
		type: 'POST',
		async: false,
		dataType: 'json',
		data: form_element.serialize()
		})
		.done(editInPlaceSuccess)
		.fail(ajaxError)
		.always(editInPlaceComplete);
}

function activeEditInPlace( event )
{

	form_element = $(this).parent();
	form_element.off('submit').on('submit', updateEditInPlace);
}

function activeButtonForEditInPlace( element )
{
	var datepickerInput = element.find('input.has_datepicker');
    if (datepickerInput.hasClass('set_readonly')) {
        datepickerInput.attr('readonly', 'readonly');
    }
    if (datepickerInput.hasClass('onselect_submit')) {
        element.find('button[type="submit"]').hide();
        datepickerInput.closest('form').on('submit', updateEditInPlace);
    }
    else {
        element.find('button[type="submit"]').off('click').on('click', updateEditInPlace);
    }
    element.find('button[type="button"]').off('click').on('click', function(){
                $(this).parent().parent().find('span').show();
                $(this).parent().parent().on('dblclick', editFiled);
                $(this).parent().remove();
    });
}

function editFiled( event )
{
	event.preventDefault();
	$(this).off('dblclick');
	element_id = $(this).attr('id');
	raw_info = element_id.split('_');

	field_info = {
		'entity': raw_info[0],
		'attribut': raw_info[1],
		'id': raw_info[2]
	}

	var that = $(this);

	$.ajax({
		url: $(this).parent().attr('data-action'),
		type: 'POST',
		async: false,
		dataType: 'html',
		data: field_info
		}).done( function( data ) {
			that.find('span').hide();
			that.append(data);
            addDatePickerToDateFields('#' + that.find('form').attr('id'));
			activeButtonForEditInPlace( that );
		});
}

var elem_first_daysoff;

function parseDate( input_day, is_morning )
{
	var f_jour = input_day[0].substring(6, 8);
	var f_mois = input_day[0].substring(4, 6);
	var f_anne = input_day[0].substring(0, 4);
	var f_heure = is_morning ? 9 : 14;


	return new Date( parseInt(f_anne), parseInt(f_mois) - 1, parseInt(f_jour), f_heure, 0, 0 );
}

function selectDaysOff( event )
{
	if(event.shiftKey)
	{
		if( elem_first_daysoff )
		{
			var reg = new RegExp("([0-9]{8})");
			var reg_month = new RegExp("([0-9]{6})");

			var start_date = reg.exec(elem_first_daysoff.attr('id'));
			var end_day = reg.exec($(this).attr('id'));

			start_is_morning = elem_first_daysoff.attr('id').substr(-9, 1) == "m" ? true : false;
			end_is_morning = $(this).attr('id').substr(-9, 1) == "m" ? true : false;
			start_date_object = parseDate( start_date, start_is_morning );
			end_date_object = parseDate( end_day, end_is_morning );

			time_diff = end_date_object.getTime() - start_date_object.getTime();
			var diff_day =  time_diff / 1000 / 60 / 60 / 12;
			if(diff_day < 0)
			{
				tmp_start = start_date_object;
				tmp_end = end_date_object;
				start_date_object = tmp_end;
				end_date_object = tmp_start;
			}

			var month_select = reg_month.exec(elem_first_daysoff.attr('id'));
			var base_id = "fullsix_projectforecastbundle_daysoffstype_";
			var jour = start_date[0].substring(6, 8);

			start_date_object = new Date( start_date_object.getTime() + 1000 * 60 * 60 * 12 );
			while ( start_date_object.getTime() < end_date_object.getTime() )
			{
				var year = start_date_object.getUTCFullYear();
				var month = start_date_object.getMonth() + 1;

				month = '0' + month.toString();
				var day = '0' + start_date_object.getUTCDate();

				suffix_id = year +''+ month.substr(-2) +''+ day.substr(-2);

				if( start_date_object.getUTCHours() >= 12)
					var id_touch = '#' + base_id + 'a'+suffix_id;
				else
					var id_touch = '#' + base_id + 'm'+suffix_id;

				if( $(id_touch).length )
				{
					if( $(id_touch).attr('checked') )
						$(id_touch).removeAttr('checked');
					else
						$(id_touch).attr('checked', "checked");
				}


				start_date_object = new Date( start_date_object.getTime() + 1000 * 60 * 60 * 12 );
			}
		}
		elem_first_daysoff = null;
	} else {
		elem_first_daysoff = $(this);
	}
}

function unselectDaysOff( event )
{
	event.preventDefault();
	$(this).parent().parent().find('.day').removeAttr('checked');
}

function addDatePickerToDateFields( selector )
{
    var element = jQuery(selector).find('.has_datepicker');
    if (element.length)
    {
        var datepickerConfig = {
            firstDay: 1,
            dateFormat: "dd-mm-yy",
            dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ],
            dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam" ],
            monthNames: [ "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ]
        };
        if (element.hasClass('onselect_submit')) {
            datepickerConfig.onSelect = function(dateText, inst){
                element.closest('form').submit();
            };
        }
        element.datepicker(datepickerConfig);
    }
}

$(function(){

	if( $('.tasksloadstypeform').length )
	{
		$('.tasksloadstypeform').on('submit', addTasksloads);
	}

	if( $('.taskstypeform').length )
	{
		$('.taskstypeform').on('submit', addTasks);
	}

	addDatePickerToDateFields('body');

	$('button.show-form').on('click', function() {
		form_to_show = $(this).parent().parent().next().find('.hidden-form');
		form_to_show.toggle();
	});

	$('.editable-field').on('dblclick', editFiled);

	if( $('.daysoff .day').length )
	{
		$('.daysoff .day').on('click', selectDaysOff);
	}

	if( $('.daysoff .unselect-days').length )
	{
		$('.daysoff .unselect-days').on('click', unselectDaysOff);
	}
});