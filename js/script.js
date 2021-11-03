(function ($, Drupal) {

  //Cambiar valores diarios al cambiar fecha

  $('#js-valores-diarios').on('change', '.js-datepicker-fecha-diarios', function(event) {

    var fecha = $(this).val();

    var res = fecha.split('/');

    fecha = res[2]+res[1]+res[0];

    var idfondo = $('#js-valores-diarios').data('idfondo');

    $('.loading').show();

    $('.js-show').hide();



    $.ajax({

      type: "GET",

      url: '/fondo-de-inversion/valores-diarios?id_fondo='+idfondo+'&fecha='+fecha,

      success : function(response) {

        $('.js-ajax').html(response.body);

        $('.loading').hide();

        $('.js-show').show();

        $( ".js-datepicker" ).datepicker({
          format: 'dd/mm/yyyy',
          language: 'es'
        });

      },

      error : function(jqXHR, status, error) {

      }

    });



  });

  // Valores diarios de fondo de inversion

  if ($('#js-valores-diarios').length > 0) {

    var idfondo = $('#js-valores-diarios').data('idfondo');

    var fecha = $('#js-valores-diarios').data('fecha');

    //$('.u-layer, .c-modal').addClass('is-active');



    $.ajax({

      type: "GET",

      url: '/fondo-de-inversion/valores-diarios?id_fondo='+idfondo+'&fecha='+fecha,

      success : function(response) {

        $('.js-ajax').html(response.body);

        $('.loading').hide();

        $('.js-show').show();

        $( ".js-datepicker" ).datepicker({
          format: 'dd/mm/yyyy',
          language: 'es'
        });

      },

      error : function(jqXHR, status, error) {

      }

    });

  }



  // Obtener datos del fondo

  fund = {};

  fund.id = $('input[name="idFondo"]').val();
  //console.log("id"+fund.id);

  fund.serie = $('select[name="serie"]').val();

  fund.limit = $('.tab a.active').attr('plazo');

  if ($('#c-graphic_fund').length) {
    get_fund_data(fund.id,fund.serie,fund.limit);
  }

  $('.tab a').click(function(){

    // tabs code

    $('.tab a').removeClass('active');

    $(this).addClass('active');

    fund.limit = $(this).attr('plazo');

    get_fund_data(fund.id,fund.serie,fund.limit, function(values, dates){
      var chart = $('#graphic_'+fund.id).highcharts();
      chart.series[0].setData(values, false);
      chart.xAxis[0].setCategories(dates);
    });

    $('#graphic_'+fund.id).highcharts().redraw();

    return false;

  });

  $(document).on('change', 'select[name="serie"]', function(event) {

    fund.serie = $(this).val();

    var fundId = $(this).data('fund-id') || fund.id;
    var limit = $(this).data('plazo') || fund.limit;

    get_fund_data(fundId,fund.serie,limit, function(values, dates){
      var chart = $('#graphic_'+fundId).highcharts();
      chart.series[0].setData(values, false);
      chart.xAxis[0].setCategories(dates);
      $('#graphic_'+fundId).highcharts().redraw();
    });

    var chartRentabilidad = $(this).data('chart-rentabilidad');

    if (chartRentabilidad) {
      $(chartRentabilidad).trigger('inversiones.loadChartRentabilidad');
    }

  });

  function get_fund_data(id,serie,limit, callback){
    //console.log("test 1: "+'ID: '+id+'SERIE: '+serie+'LIMIT: '+limit+'CALLBACK: '+callback);
    $.ajax({

      type: "GET",
      url: '/informacion-fondo/?IdFondo='+id+'&IdSerie='+serie+'&Plazo='+limit,
      success : function(response) {

        //data = JSON.parse(response);
        data = response;

        var dates = [], values = [];
        $.each(data, function (key,value) {
          dates.push(value.Fecha);
          values.push(value.Valor);
        });
        init_graphic(id, dates, values);

        if (typeof callback !== 'undefined') {
          callback(values, dates);
        }
      },

      error : function(jqXHR, status, error) {

        // console.log(error);

      }

    });

  }

  // Iniciar gráfico

  function init_graphic(fundId,fundDates,fundValues) {
    //console.log('init_graphic: '+fundId+fundDates+fundValues);

    dates = fundDates;

    value = fundValues;

    Highcharts.setOptions({
      colors: ['#2B0D61']
    });

    Highcharts.chart('graphic_'+fundId, {

      chart: {

        type: 'spline',

        height: 205,

        marginLeft: 60

      },

      title: {

        text: ' '

      },

      credits: {

        enabled: false

      },

      subtitle: {

        text: ' '

      },

      xAxis: {

        categories: dates

      },

      yAxis: {

        className: 'highcharts-color-0',

        alternateGridColor: '#EAEBEC',

        tickAmount: 6,

        title: {

          text: ''

        },

        labels:{

          align:'right',

          padding:10,

          overflow: 'justify'

        }

      },

      legend: {

        layout: 'vertical',

        floating: true,

        backgroundColor: '#FFFFFF',

        align: 'left',

        verticalAlign: 'top',

        y: 60,

        x: 90

      },

      tooltip: {

        formatter: function () {

          return this.y;

        }

      },

      plotOptions: {

      },

      legend: {

        enabled: false

      },

      series: [{

        data: value

      }],

      navigation: {

        buttonOptions: {

          enabled: false

        }

      }

    });

  }

  // Función solicitud de contacto perfil inversionista

  $('body').on('submit', '#solicitud_contacto', function(event) {

    event.preventDefault();

    $.ajax({

      type: "POST",

      url: '/descubre-perfil-inversionista/email',

      data: $(this).serialize(),

      success : function() {

        var data = $("#solicitud_contacto").serialize().split("&");

        var obj = {};

        for(var key in data) {

          obj[data[key].split("=")[0]] = data[key].split("=")[1];

        }

        $('.c-simulation-steps').hide();

        $('#result').hide();

        $('#message').find('#user').html(obj.nombre);

        $('#message').show();



      },

      error : function(jqXHR, status, error) {

        // alert(jqXHR, status, error);

        // add actions on error

      }

    });

  });

  function simulator_now(form) {

    $('#js-message-simulator').hide();

    var simulation = {};

    simulation.form = form;

    simulation.inputs = $(simulation.form).find('input');

    simulation.answers = new Array();

    if ( $(simulation.form) ) {

      // crea array de datos

      var data = {}

      // var isvalid = false;

      $(simulation.form).find('input, simulator').each(function(index, element) {

        key = $(element).attr('name');

        value = $(element).val();

        data[key] = value;

      });

      simulation.answers.push(data);

      // Acción

      $(simulation.form).on('click', '.js-simulation-btn', function(event) {

        event.preventDefault();

        var service = $(this).attr('service')+'?'+($(this).closest('form').serialize());

        if (!$(this).hasClass('o-btn--disable')) {

          $.ajax({

            url: service,

            dataType: "text",

            beforeSend: function(){

              $('#result').html('<div class="loading"><div class="lds-css"><div class="lds-dual-ring"><div></div></div></div>');

            },

            success: function(response, status, xhr){

              value = [];

              label = ['Sin APV', 'Con APV'];

              $('#result').html(response);

              sinapv = parseInt($('#graphic_simulation_pension').attr('sinapv').replace(/\./g, ''));

              conapv = parseInt($('#graphic_simulation_pension').attr('conapv').replace(/\./g, ''));

              value.push(sinapv,conapv);

              init_graphic_simulator('graphic_simulation_pension', value, label);

            },

            error: function (status, xhr) {

              // console.log(status, xhr);

            }

          });

        }

      });

    }

  }



  // SIMULADOR PENSION

  simulator_now('#apv_simulator_now');



  //simulation

  //hide

  $('.js-simulation-02,.js-simulation-03,.js-simulation-04,.js-simulation-05, .js-simulation-result, .js-amount-03, .js-amount-04, .js-amount-05').hide();



  // js-simulation-positive-number

  $('.js-simulation-positive-number').on('keyup change', function(){

    var value = parseInt($(this).val());



    if (!value || value < 0) {

      $(this).val(0);

    }

  });





  //paso 1

  $('.c-simulation').on('keyup', '.js-simulation-01-input', function(event) {

    var isValid;

    value = $(event.target).val();

    if ($(event.target).hasClass('js-amount')) {

      $(event.target).val(numberFormat(value));



    }

    $(".js-simulation-01-input").each(function() {

      var element = $(this);

      if (element.val() == "") {

        isValid = false;

      }

    });

    if(isValid != false){

      $('.js-simulation-02').slideDown();

      if($('.js-simulation-05').is(':visible')){

        $('.js-simulation-btn').removeClass('o-btn--disable');

      }

    }else{

      $('.js-simulation-btn').addClass('o-btn--disable');

    }



  });



  //paso 2

  $('.c-simulation').on('keyup', '.js-simulation-02 input', function(event) {

    value = $(event.target).val();

    if ($(event.target).hasClass('js-amount')) {

      $(event.target).val(numberFormat(value));

    }

    $('.js-simulation-03').slideDown();

  });



  //paso 3

  $('.c-simulation').on('change', '.js-simulation-03-select', function(event) {

    if($(this).val() == '1'){

      $('.js-amount-03').fadeIn();

    }else if($(this).val() == 'empty'){

      $('.js-simulation-btn').addClass('o-btn--disable');

    }else{

      $('.js-amount-03').fadeOut();

      $('.js-simulation-04').slideDown();

    }

  });

  $('.c-simulation').on('keyup', '.js-simulation-03 input', function(event) {

    value = $(event.target).val();

    if ($(event.target).hasClass('js-amount')) {

      $(event.target).val(numberFormat(value));

    }

    $('.js-simulation-04').slideDown();

  });



  //paso 4

  $('.c-simulation').on('change', '.js-simulation-04 select', function(event) {



    if($(this).val() == '1'){

      $('.js-amount-04').fadeIn();

    }else if($(this).val() == 'empty'){

      $('.js-simulation-btn').addClass('o-btn--disable');

    }else{

      $('.js-amount-04').fadeOut();

      $('.js-simulation-05').slideDown();

    }

  });

  $('.c-simulation').on('keyup', '.js-simulation-04 input', function(event) {

    value = $(event.target).val();

    if ($(event.target).hasClass('js-amount')) {

      $(event.target).val(numberFormat(value));

    }

    $('.js-simulation-05').slideDown();

  });



  //paso 5

  $('.c-simulation').on('change', '.js-simulation-05 select', function(event) {

    if($(this).val() == '1'){

      $('.js-amount-05').fadeIn();

    }else if($(this).val() == 'empty'){

      $('.js-simulation-btn').addClass('o-btn--disable');

    }else{

      $('.js-amount-05').fadeOut();

      $('.js-simulation-btn').removeClass('o-btn--disable');

    }

  });

  $('.c-simulation').on('keyup', '.js-simulation-05 input', function(event) {

    value = $(event.target).val();

    if ($(event.target).hasClass('js-amount')) {

      $(event.target).val(numberFormat(value));

    }

    $('.js-simulation-btn').removeClass('o-btn--disable');


});

  // Graficos simulator

  function init_graphic_simulator(graph, value, categories) {

    // console.log(graph, value, label);

    if ($('#'+graph).length) {

      Highcharts.setOptions({

        colors: ['#4C49AA']

      });

      Highcharts.chart(graph, {

        chart: {

          type: 'column',

          height: 255

        },

        title: {

          text: ' '

        },

        credits: {

          enabled: false

        },

        subtitle: {

          text: ' '

        },

        xAxis: {

          categories: categories

        },

        yAxis: {

          className: 'highcharts-color-0',

          alternateGridColor: '#EAEBEC',

          tickAmount: 6,

          title: {

            text: ' '

          }

        },

        tooltip: {

          formatter: function() {

            return '$' + this.y;

          }

        },

        plotOptions: {},

        legend: {

          enabled: false

        },

        series: [{

          data: value

        }],

        navigation: {

          buttonOptions: {

            enabled: false

          }

        }

      });



    }

  }

})(jQuery, Drupal);



