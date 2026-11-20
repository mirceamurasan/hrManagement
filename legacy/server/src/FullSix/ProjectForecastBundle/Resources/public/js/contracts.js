var collectionHolder = $('ul.periods');
var $addPeriodLink = $('<a href="#" class="add_period_link btn btn-small btn-primary">Add a period</a>');
var $newLinkLi = $('<li></li>').append($addPeriodLink);

function addPeriodForm(collectionHolder, $newLinkLi) {
    var prototype = collectionHolder.attr('data-prototype');

    var newForm = prototype.replace(/__name__/g, collectionHolder.children().length);

    var $newFormLi = $('<li class="formPeriods"></li>').append(newForm);
    $newLinkLi.before($newFormLi);

    var typeConSelect = $('#fullsix_projectforecastbundle_contractstype_contracttype')[0];
    onContractTypeChange(typeConSelect);

    addPeriodFormDeleteLink($newFormLi);
}

function addPeriodFormDeleteLink($periodFormLi) {
    var $removeFormA = $('<a href="#" class="btn btn-small btn-inverse">Delete period</a>');
    $periodFormLi.append($removeFormA);

    $removeFormA.on('click', function(e) {
        e.preventDefault();

        $periodFormLi.remove();
    });
}


collectionHolder.append($newLinkLi);

collectionHolder.find('li.formPeriods').not(':first-child').each(function() {
    addPeriodFormDeleteLink($(this));
});

$addPeriodLink.on('click', function(e) {
    e.preventDefault();

    addPeriodForm(collectionHolder, $newLinkLi);
    // Load DATEPICKER
	$( ".has_datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });
});


// Load DATEPICKER
$( ".has_datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });

// autocomplete
$( "#fullsix_projectforecastbundle_contractstype_idworker_lastname" ).autocomplete({
    source	 : function( request, response ) {
        $.ajax({
          url: "/contracts/search.html",
          dataType: "json",
          type: "POST",
          data: {
            lastname: request.term
          },
          success: function( data ) {
            response( $.map( data.names, function( item ) {
              return {
				label: item.lastname +' '+ item.firstname,
                value: item.lastname,
                firstname: item.firstname
              }
            }));
          }
        });
    },
    minLength: 2,
    search	 : function(){$(this).addClass('working');},
	open     : function(){$(this).removeClass('working');},
    dataType : "json",
	select	 : function( event, ui ) {
		$( "#fullsix_projectforecastbundle_contractstype_idworker_firstname" ).val(ui.item.firstname);
    }
});

/* ---------- Periods ------- */

function onContractTypeChange(typeConSelect)
{
    var typeConId = typeConSelect.value;
    $.ajax({
        dataType: "json",
        type: "POST",
        url: 'get_typeCon_external',
        data: {'typeConId': typeConId},
        success: function(response) {
            if (response['status'] == "OK") {
                var bdc = $('#fullsix_projectforecastbundle_contractstype_bdc')[0];
                var endDates = $('input.end-date');
                var tjms = $('input.tax');
                if (response['external'] == true) {
                    bdc.required = true;
                    bdc.parentElement.style.display="inline";
                    for (var i=0; i < endDates.length; i++) {
                        endDates[i].required = true;
                    }
                    for (var i=0; i < tjms.length; i++) {
                        tjms[i].required = true;
                        tjms[i].parentElement.style.display="inline";
                    }
                }else{
                    bdc.required = false;
                    bdc.parentElement.style.display="none";
                    for (var i=0; i < endDates.length; i++) {
                        endDates[i].required = false;
                    }
                    for (var i=0; i < tjms.length; i++) {
                        tjms[i].required = false;
                        tjms[i].parentElement.style.display="none";
                    }
                }
            }
        }
    });
}

$(document).ready(function() {
    var typeConSelect = $('#fullsix_projectforecastbundle_contractstype_contracttype')[0];
    onContractTypeChange(typeConSelect);

});

$(document).on('change', '#fullsix_projectforecastbundle_contractstype_contracttype', function() {
    onContractTypeChange(this);
});