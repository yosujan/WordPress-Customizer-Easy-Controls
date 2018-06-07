jQuery(document).ready(function($){

  $('.custom-control-heading').on('click', function(){

    $(this).parent('li').find('.custom-control-body').slideToggle();

  })



$('.sortable-list').sortable({
  connectWith: '.sortable-list',
  handle: ".custom-control-heading",
  update: function( event, ui ) { 
      updated_values($(event.target));
  }
});



$('.add-new').click(function(){

    var ulselector = $(this).closest('.repeater-wrapper').find('ul.sortable-list');

    var idd = get_new_updated_id(ulselector, 'val-');

    
    //$('.sortable-list').find('li').first().attr('id', idd);

    var colne_elem = ulselector.find('li').first().clone(true); 

    colne_elem.attr('id', idd);

    colne_elem.appendTo(ulselector);

    updated_values(ulselector);

    //$('.sortable-list').find('li').first().clone(true).appendTo('.sortable-list'); 



});

$('.delete').click(function(){

var ulselector = $(this).closest('.repeater-wrapper').find('ul.sortable-list');
//  $(this).closest('.all-sub-container').remove();

$.when($(this).closest('.all-sub-container').remove()).then( updated_values(ulselector) );

});


/*$('ul.sortable-list').find('select').on('change', function(){

  updated_values($(this).closest('.repeater-wrapper').find('ul.sortable-list'));

}); */

$('ul.easy-control').find(':input').bind('change', function(){
  updated_values($(this).closest('.repeater-wrapper').find('ul.sortable-list')); //$(this).closest('.repeater-wrapper').find('ul.sortable-list')
});

function updated_values(ulselector){

var return_json;
var temp = new Array();

setTimeout(function() {

      var sortable_elem = ulselector; //$('.sortable-list');
      //var changedList = sortable_elem.attr('id');
      var order = ulselector.sortable('toArray');

      var positions = order.join(';');

     var return_array = new Array();

     order.forEach(function(item, index){

      var single_node = new Array();
      var main_value = 0;
      var sub_fields = new Array();
      var countt=0;

      var input_val = (ulselector).find('#'+item).find(':input');

      input_val.each(function(index){

        if ($(this).is(':radio')) {

          if($(this).is(':checked')){

              var tempid=$(this).attr('class');
              var temp3 = { [tempid] : $(this).val() };
              sub_fields.push(temp3);
          }
          else{

          }
        }
        else
        {

          if($(this).hasClass('main-value')){
             main_value=$(this).val();
          }
          else
          {
             var tempid=$(this).attr('name');
             console.log(tempid);
             var temp3 = { [tempid] : $(this).val() };
             if(tempid) sub_fields.push(temp3);
            
          }
         
        }

      }) // foreach for input values ends


      single_node= {mainValue:main_value, sub_fields:sub_fields};

      console.log(single_node);

      return_array.push(single_node);




     }) // foreach for LI for sortable ends



    //console.log(temp);

    var myJSON = JSON.stringify(return_array);
    console.log(myJSON);

    ulselector.closest('.customize-control-easy-control').find('.main-value-input').val(myJSON);

    ulselector.closest('.customize-control-easy-control').find('.main-value-input').trigger('change');

    //$('#main-value-setting-1').val(myJSON);
    //$('#main-value-setting-1').trigger('change');
  
  }, 10);


}

function get_new_updated_id(selector, prefix){

var count = selector.find('li').length;

count++;

//console.log(prefix + count);

return prefix + count;

}


})