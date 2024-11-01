jQuery(document).ready(function ($) {
  var x = $(document);
  var type = jQuery('span.legend-label.active').attr('data-type');
  var graphrowId = jQuery('.chart-view').attr('graphid');
  WsuGetGraphResult(graphrowId, type);


  new ClipboardJS('#copytext');


  x.on('click', '#copytext', function () {
    $('.tooltiptext').text('Copied!');
    $('.tooltiptext').css({ "visibility": "visible", "opacity": "1" });
    setTimeout(() => {
      $('.tooltiptext').text('Copy to clipboard');
      $('.tooltiptext').css({ "visibility": "", "opacity": "" });
    }, 1000);


  });

  /**
  * Get published post by post type
  */
  x.on('change', '#wsu-post-type', function () {
    var postType = $('#wsu-post-type').find(":selected").val();
    wsu_get_all_post(postType, 0);
  });

  function wsu_get_all_post(type, id) {
    $.ajax({
      url: wsu.ajaxurl,
      type: 'post',
      data: {
        'action': 'wsurl_get_posts',
        'type': type,
        'id': id
      },
      success: function (response) {
        $('#wsu-post-name').html(response);
      },
    });
  }
  /**
  * Submit Form page and post
  */
  x.on('click', '#wsu_add_item', function (e) {
    e.preventDefault();
    var shorturltype = $('input:radio[name="typeofurl"]:checked').val();
    var ptype = $('#wsu-post-type');
    var pname = $('#wsu-post-name');
    var ppcu = $('#post_page_custom_url');
    var title = $('#wsu_custom_title');
    var formdata = $('#wsu-shourt-url-form').serialize();

    if (ptype.val() == '-1') { ptype.focus(); return false; }
    if (title.val() == '') { title.focus(); return false; }
    if (pname.val() == '-1') { pname.focus(); return false; }
    if (shorturltype == 'custom_selection') {
      if (ppcu.val() == '') { ppcu.focus(); return false; }
    }

    $.ajax({
      url: wsu.ajaxurl,
      type: 'post',
      data: {
        'action': 'wsu_add_item',
        'data': formdata
      },
      success: function (response) {
        var rowcount = $('.wsu-short-url-list').length;
        var UpdateList = $('#wsu-post-action').val();
        if (rowcount > 0) {
          if (UpdateList > 0) {
            $(".wsu-list-lavel-" + UpdateList).replaceWith(response.html);
            $('#wsu-post-action').val(0);
            $('#wsu_add_item').val('Create Short URL');
            $('.wsu-graph-title').text(response.title);
            $('.wsu-graph-fullurl').text(response.targeturl);
            $('#ShortUrl .wp-graph-url').text(response.shorturl);
            $('#copytext').attr('data-clipboard-text', response.shorturl);
          } else {
            $(".wsu-short-url-list:first-child").before(response.html);
          }

          ;
        }
        else {
          $(".wsu-append-list").html(response.html);
          $(".wsu-short-url-list:first-child").trigger("click");
        }
        $('.wsu-ajax-submit-response').html('<p>' + response.msg + '</p>').fadeIn();
        wsu_reset_form();
        setTimeout(() => {
          $('.wsu-ajax-submit-response').fadeOut();
          $(".close").trigger("click");
        }, 1000);
      },
    });
  });

  /**
    * Submit Form Custom url
    */
  x.on('click', '.wsu-legend-cart span', function (e) {
    var rowid = jQuery('.chart-view').attr('graphid');
    $('.legend-label').removeClass('active');
    $(this).addClass('active');
    var type = $(this).attr('data-type');
    WsuGetGraphResult(rowid, type);
  });

  x.on('click', '#wsu_add_item_custom', function (e) {
    e.preventDefault();
    var title = $('#customtitle');
    var traget = $('#fullcustomurl');
    var surl = $('#wsu-custom-url');
    var form = $('#wsu-shourt-url-form-custom-url').serialize();
    if (title.val() == '') { title.focus(); return false; }
    if (traget.val() == '') { traget.focus(); return false; }
    if (surl.val() == '') { surl.focus(); return false; }
    $.ajax({
      url: wsu.ajaxurl,
      type: 'post',
      data: {
        'action': 'wsu_add_item_custom',
        'data': form
      },
      success: function (response) {
        var rowcount = $('.wsu-short-url-list').length;
        var UpdateList = $('#wsu-custom-action').val();
        if (rowcount > 0) {
          if (UpdateList > 0) {
            $(".wsu-list-lavel-" + UpdateList).replaceWith(response.html);
            $('#wsu-custom-action').val(0);
            $('#wsu_add_item_custom').val('Create Short URL');
            $('.wsu-graph-title').text(response.title);
            $('.wsu-graph-fullurl').text(response.targeturl);
            $('#ShortUrl').text(response.shorturl);
            $('#copytext').attr('data-clipboard-text', response.shorturl);
          } else {
            $(".wsu-short-url-list:first-child").before(response.html);
          }
        }
        else {
          $(".wsu-append-list").html(response.html);
          $(".wsu-short-url-list:first-child").trigger("click");
        }
        wsu_reset_form();
        $('.wsu-custom-ajax-submit-response').html('<p>' + response.msg + '</p>').fadeIn();
        setTimeout(() => {
          $('.wsu-custom-ajax-submit-response').fadeOut();
          $(".close").trigger("click");
        }, 1000);
      },
    });
  });


  /**
  * Manage Categories
  */

  x.on('click', '#wsu_add_categories', function (e) {
    e.preventDefault();
    var title = $('#catname');
    var Action = $('#wsucataction').val();
    var OptionClass = $('select[name=wsu_categories_action]').find('option:selected').attr('class');
    var updateCat = $('select[name=wsu_categories_action]');
    var form = $('#wsu-shourt-url-form-manage-categories').serialize();
    if (title.val() == '' && Action != 'delete') { title.focus(); return false; }
    if (Action != 'create') {
      if (updateCat.val() == '-1') { updateCat.focus(); return false; }
    }
    $.ajax({
      url: wsu.ajaxurl,
      type: 'post',
      data: {
        'action': 'wsu_add_categories',
        'data': form
      },
      success: function (response) {
        $('.wsu-categories-ajax-submit-response').html('<p>' + response.msg + '</p>').fadeIn();
        if (Action == 'delete') {
          $('.' + OptionClass).remove();
        }
        if (Action == 'update') {
          $('.' + OptionClass).replaceWith(response.html);
        }

        if (Action == 'update') {
          $('.' + OptionClass).replaceWith(response.html);
        }
        if (Action == 'create') {
          $('select[name=wsu_categories]').append(response.html);
          $('select[name=wsu_categories_action]').append(response.html);
        }
        setTimeout(() => {
          $(".close").trigger("click");
          $('.wsu-categories-ajax-submit-response').fadeOut();
          wsu_reset_form();
        }, 1000);
      },
    });
  });
  /**
 * Graph result
 */
  x.on('click', '.wsu-short-url-list', function (e) {
    var rowid = jQuery(this).attr('data-row-id');
    $('.chart-view').attr('graphid', rowid);
    var type = $('span.legend-label.active').attr('data-type');
    jQuery(this).siblings().removeClass('wsu-active-item');
    jQuery(this).addClass('wsu-active-item');
    WsuGetGraphResult(rowid, type);
  });

  function WsuGetGraphResult(rowid, type) {
    $.ajax({
      url: wsu.ajaxurl,
      type: 'post',
      dataType: "json",
      data: {
        'action': 'wsu_get_graph_result',
        'rowid': rowid,
        'type': type
      },
      success: function (response) {
        if ($.isEmptyObject(response.fullurl)) {
          console.log('No result Found !');
        } else {
          $('.wsu-graph-view').show();
          $('.wsu-graph-heading .wp-graph-url').text(response.shorturl);
          $('.wsu-graph-heading .wp-graph-actions .wp-graph-action-delete').attr("data-rowid", response.rowID);
          $('.wsu-graph-title').text(response.title);
          $('.wsu-graph-fullurl').text(response.fullurl);
          $('#copytext').attr('data-clipboard-text', response.shorturl);
          var weekdays = [];
          var hit = [];
          $.each(response.graph, function (k, v) {
            weekdays.push(v.weekday);
            hit.push(v.hiturl);
          });
          document.getElementById("chartContainer").innerHTML = '<canvas id="myChart"></canvas>';
          var ctx = document.getElementById("myChart").getContext('2d');
          var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
              labels: weekdays,
              datasets: [{
                label: 'Clicks',
                data: hit,
                backgroundColor: "#5b9dd9"
              },]
            },
            options: {
              responsive: true,
              legend: {
                display: false
              },
              scales: {
                yAxes: [{
                  id: 'y-axis-label',
                  ticks: {
                    min: 0,
                    stepSize: 1,
                  },
                  position: 'left',

                }]
              },
              tooltips: {
                callbacks: {
                  label: function (tooltipItem, data) {
                    console.log(tooltipItem);
                    return "" + Number(tooltipItem.yLabel) + " Click";
                  }
                }
              }
            }
          });
        }
      },
    });
  }


  $('input:radio[name="typeofurl"]').change(function () {
    $('.wsu-show-all').hide();
    var showClass = $(this).attr('data-show');
    $('.' + showClass).show();
  });

  // modal js
  x.on('click', '#url-create-button', function (e) {
    wsu_reset_form();
    var modal = $(this).attr('data-modal');
    $('#' + modal).show();

  });

  x.on('click', '.close', function (e) {
    var modal = $(this).attr('data-modal');
    $(this).closest('.modal').hide();
  });

  $('#wsu-form-tabs').each(function () {
    var $active, $content, $links = $(this).find('a');
    $active = $($links[0]);
    $active.addClass('active');
    $content = $($active[0].hash);
    $links.not($active).each(function () {
      $(this.hash).hide();
    });

    $(this).on('click', 'a', function (e) {
      $active.removeClass('active');
      $content.hide();
      $active = $(this);
      $content = $(this.hash);
      $active.addClass('active');
      $content.show();
      e.preventDefault();
    });
  });

  x.on('keyup', '#searchshorturl', function () {
    getSearchQuery = $('#searchshorturl').val();
    selectedCat = $('#wsu_categories').val();

    $('[data-row="wsu-list-wrapper"]').hide();
    if (getSearchQuery.length > 2) {
      $('[data-row="wsu-query-list-wrapper"]').hide();

      $.ajax({
        url: wsu.ajaxurl,
        data: {
          'action': 'wsu_data_search',
          'cat': selectedCat,
          'query': getSearchQuery

        },
        success: function (data) {
          $('[data-row="wsu-list-wrapper"]').html(data);
          $('[data-row="wsu-list-wrapper"]').show();
        }
      });
    } else {

      $('[data-row="wsu-query-list-wrapper"]').show();
    }
  });

  x.on('change', '#wsu_categories', function () {
    getSearchQuery = $('#searchshorturl').val();
    selectedCat = $('#wsu_categories').val();
    $('[data-row="wsu-query-list-wrapper"]').hide();
    $.ajax({
      url: wsu.ajaxurl,
      data: {
        'action': 'wsu_data_search',
        'cat': selectedCat,
        'query': getSearchQuery
      },
      success: function (data) {
        $('[data-row="wsu-list-wrapper"]').html(data);
      }
    });
  });


  x.on('click', '#wsu-edit-record', function () {
    var graphrowId = jQuery('.chart-view').attr('graphid');
    $.ajax({
      url: wsu.ajaxurl,
      dataType: "json",
      type: 'post',
      data: {
        'action': 'wsu_get_edit_record',
        'rowid': graphrowId
      },
      success: function (response) {
        $("#url-create-button").trigger("click");
        if (response.data.post_type == 'wsu-custom') {
          $('.site_url_and_customUrl select#wsu_categories').val(response.data.catid);
          $('#customtitle').val(response.data.title);
          $('#fullcustomurl').val(response.data.custom_url);
          $('#wsu-custom-url').val(response.data.short_slug);
          $('#wsu_add_item_custom').val('Update Short Url');
          $('#wsu-custom-action').val(response.data.ID);
          $("#tab1-tab").trigger("click");
        } else {
          $("#wsu-post-type").val(response.data.post_type);
          wsu_get_all_post(response.data.post_type, response.data.post_id)
          $(".wsu-post-page-cat").val(response.data.catid);
          $("#wsu_custom_title").val(response.data.title);
          $("#post_page_custom_url").val(response.data.short_slug);
          $("input[name=typeofurl][value=custom_selection]").attr('checked', 'checked');
          $('.wsu-custom-short-url-wrap').show();
          $('#wsu_add_item').val('Update Short Url');
          $('#wsu-post-action').val(response.data.ID);
          $("#tab2-tab").trigger("click");
        }
      },
    });
  });


  x.on('click', '#first_link', function (e) {
    e.preventDefault();
    $("#url-create-button").trigger("click");
    wsu_reset_form();
  });


  x.on('change', '#wsucataction', function (e) {
    e.preventDefault();
    var action = $(this).val();
    var onaction = $(this).parents().next().next();
    if (action != 'create') {
      onaction.children("#wsu_categories").removeAttr("disabled");
    } else {
      onaction.children("#wsu_categories").attr("disabled", "disabled");
    }
    if (action == 'delete') {
      onaction.next().next().children("#catname").attr("disabled", "disabled");
    } else {
      onaction.next().next().children("#catname").removeAttr("disabled");
    }
  });

  function wsu_reset_form() {
    $('#wsu_categories').val('-1');
    $('#customtitle').val('');
    $('#fullcustomurl').val('');
    $('#wsu-custom-url').val('');
    $('.wsu-post-page-cat').val('-1');
    $('#wsu-post-type').val('-1');
    $('#wsu-post-type').val('-1');
    $("input[name=typeofurl][value=autogenerate_selection]").attr('checked', 'checked');
    $('#wsu_custom_title').val('');
    $('#wsu-post-name').val('-1');
    $('#wsucataction').val('create');
    $('select[name=wsu_categories_action]').val('-1');
    $('#catname').val('');
    $('#tab1-tab').trigger("click");
  }


  //Delete Short URL Entery
  jQuery(document).on('click', '[data-action="delete"]', function () {

    var rowId = jQuery(this).attr('data-rowid');
    var rowType = jQuery(this).attr('data-type');
    var getConfirm = confirm("Are you sure ?");

    if (true === getConfirm) {

      $.ajax({
        url: wsu.ajaxurl,
        type: 'post',
        data: {
          'action': 'wsu_action_delete',
          'rowid': rowId,
          'rowtype': rowType
        },
        success: function (response) {
          location.reload();
        },
      });

    }
  });

  //Delete Short URL Entery
  jQuery(document).on('click', '#wsu__add-category', function (e) {
    e.preventDefault();

    var catName = jQuery("#wsu-shoturl-categories #wsu__catname").val();
    var dataAction = jQuery("#wsu-shoturl-categories").attr('data-action');

    if (catName.length < 3) {
      jQuery("#wsu-shoturl-categories #wsu__catname").addClass("error")
    } else {
      jQuery("#wsu-shoturl-categories #wsu__catname").removeClass("error")
      $.ajax({
        url: wsu.ajaxurl,
        type: 'post',
        data: {
          'action': 'wsu_categoy_action',
          'catname': catName,
          'dataaction': dataAction,
          'rowid': 0
        },
        success: function (response) {
          jQuery("#wsu-shoturl-categories .wsu--action-status").html(response.msg);
          jQuery("#wsu-shoturl-categories .wsu--action-status").attr('id', 'wsu--' + response.status);

          setTimeout(() => {
            location.reload();
          }, 2000);


        },
      });
    }
  });

  //Open Category Form
  jQuery(document).on('click', '#wsu-add-category-btn', function () {
    jQuery("form#wsu-shoturl-categories").fadeIn('slow');
  });

  /**
   * Category Action - Edit
   */
  jQuery(document).on("click", ".wp-category-action-edit", function () {
    jQuery(this).parents(".wsu-short-url-cat-list").find(".wsu-category-input").addClass('active');
    jQuery(this).siblings(".wp-category-action-submit").fadeIn();
  });


  //Update Category Entery
  jQuery(document).on('click', '[data-action="update"]', function () {

    var $this = jQuery(this);
    var rowType = $this.attr('data-type');
    if (rowType == 'cat') {
      var rowId = $this.attr('data-rowid');
      var rowAction = $this.attr('data-action');
      var getData = $this.parents(".wsu-short-url-cat-list").find(".wsu-category-input").val();

      $.ajax({
        url: wsu.ajaxurl,
        type: 'post',
        data: {
          'action': 'wsu_categoy_action',
          'rowid': rowId,
          'dataaction': rowAction,
          'catname': getData,
        },
        success: function (response) {
          $this.parents(".wsu-short-url-cat-list").find(".wsu--response-msg").html(response.msg);
          setTimeout(() => {
            location.reload();
          }, 2000);
        },
      });
    }

  });

});



