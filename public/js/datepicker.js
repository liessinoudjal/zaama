$('input.date-birth').datepicker({
    format: "dd-mm-yyyy",
    startView: 2,
    clearBtn: true,
    language: "fr",
    autoclose: true,
    toggleActive: true,
    endDate: "-18y",
    defaultViewDate: "-18y",
    startDate: "-60y"
});

$('input.date-sortie').datepicker({
    format: "dd-mm-yyyy",
    startView: 0,
    clearBtn: true,
    language: "fr",
    autoclose: true,
    toggleActive: true,
    startDate: new Date(),
    endDate: "+1y",
    todayHighlight: true,
});

$('div#calendrier').datepicker({
    language: "fr",
    startDate: new Date(),
    endDate: "+1y",
    todayHighlight: true,
    disableTouchKeyboard:true,
    leftArrow: '<i class="fas fa-arrow-circle-left"></i>',
    rightArrow: '<i class="fas fa-arrow-circle-right"></i>',
    title: "calendrier des sorties",
    todayBtn: "linked"
    //changeDate: 
    
});
$('div#calendrier').datepicker()
  .on('changeDate', function(ev){
    console.log($(ev.date));
   console.log($(ev.date.valueOf()));
   console.log(ev.date.toJSON());
  });