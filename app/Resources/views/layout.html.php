<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <title><?php $view['slots']->output("title", "Welcome") ?> - ShenBridge</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons"/>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined"/>
    <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.blue-pink.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.5.0/dialog-polyfill.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css" media="print" onload="this.media='all'"/>

        <!--<link href="/bundles/framework/css/bootstrap.css" rel="stylesheet">-->
        <link href="/bundles/framework/css/grid.css" type="text/css" rel="stylesheet"/>
        <link href="/bundles/framework/css/style.css" type="text/css" rel="stylesheet"/> 
        <link href="/bundles/framework/css/device.css" type="text/css" rel="stylesheet"/>          
        <link href="/bundles/framework/slick/slick.css" type="text/css" rel="stylesheet"/>
        <link href="/assets/css/custom.css" type="text/css" rel="stylesheet"/>
        <link href="/bundles/framework/css/jquery.mCustomScrollbar.min.css" rel="stylesheet"/>
        <link href="/bundles/framework/css/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
        <link href="https://cdn.jsdelivr.net/jquery.ui.timepicker.addon/1.4.5/jquery-ui-timepicker-addon.min.css" rel="stylesheet"/>
    	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.css" media="print" onload="this.media='all'"/>
    	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css"/>

    <?php foreach($view['app']->css as $stylesheet): ?>
    	<link href="<?= $stylesheet ?>" rel="stylesheet"/>
    <?php endforeach ?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<?php if($view['app']->isLogged()): ?>
	<script>
		let rocketChatIP = "tradetoshare.com";
		let rocketChatPort = "8080";
		let authToken = "<?= $view['app']->authToken() ?>";
	</script>
 	<script src="/assets/js/rocketchat.js" type="text/javascript"></script>
 	<script src="https://cdnjs.cloudflare.com/ajax/libs/ion-sound/3.0.7/js/ion.sound.min.js"></script>

<script>
ion.sound({
    sounds: [
        {name: "door_bell"}
    ],
    path: "https://cdnjs.cloudflare.com/ajax/libs/ion-sound/3.0.7/sounds/",
    preload: true,
    multiplay: true,
    volume: 0.9
});
</script>

<?php endif ?>
    <script src="/bundles/framework/slick/slick.min.js" type="text/javascript"></script>
    <script src="/bundles/framework/js/jquery.mCustomScrollbar.concat.min.js"></script>
 	<script src="/bundles/framework/js/script.js" type="text/javascript"></script>
 	<script async src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js" type="text/javascript"></script>

    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>
    <meta name="MobileOptimized" content="980" />
    <meta name="HandheldFriendly" content="false" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <link rel="icon" type="image/x-icon" href="/favicon.ico"/>
</head>

<body>

   <div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">

<?= $view->render("::header.html.php") ?>

<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId      : '164967570815052',
            xfbml      : true,
            version    : 'v2.12'
        });
        FB.AppEvents.logPageView();
    };

    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>

    <main id="wrapper" class="mdl-layout__content">
        <div id="conteiner">
            <div class="center">
                <?php if ($view['app']->isLogged()): ?>
                	<?php if ($view['slots']->has("left_column")): ?>
                    	<?= $view->render("::left_column.html.php") ?>
                    <?php endif ?>
                	<?php if ($view['slots']->has("right_column")): ?>
                    	<?= $view->render("::right_column.html.php") ?>
                    <?php endif ?>
                <?php endif ?>
                <div class="colum_center" id="<?php $view['slots']->output("page_id", "article") ?>">
                    <?php $view['slots']->output("_content") ?>
                </div>

            </div><!-- center -->
        </div><!-- #conteiner -->
        
        <?= $view->render("::footer.html.php") ?>
        
    </main><!-- #wrapper -->

<!--
<main>
    <div class="row register_page">
        <div class="col-md-10 content-page">
            {% if app.session.flashBag.has('success') %}
                <div class="alert alert-success">
                    {% for msg in app.session.flashBag.get('success') %}
                        {{ msg }}
                    {% endfor %}
                </div>
            {% endif %}
            {% if app.session.flashBag.has('error') %}
                <div class="alert alert-danger">
                    {% for msg in app.session.flashBag.get('error') %}
                        {{ msg }}
                    {% endfor %}
                </div>
            {% endif %}
            <?php if ($view['app']->isLogged()): ?>
                <div class="timeline_tabs">
                    <ul>
                        <li><a href="<?= $view['router']->path('user_show', array('user' => $view['app']->getUserId())) ?>" class="active">My Profile</a></li>
                        <li><a href="<?= $view['router']->path('tradeland_invite') ?>Invite Friends</a></li>
                        <li><a href="<?= $view['router']->path('user_network') ?>My Network</a></li>
                        <li><a href="<?= $view['router']->path('tradeland_index') ?>My tradelands</a></li>
                        <li><a href="<?= $view['router']->path('user_companies') ?>My companies</a></li>
                        <li><a href="<?= $view['router']->path('message_index') ?>My message</a></li>
                    </ul>
                </div>
            <?php endif ?>

        </div>
    </div>
</main>
-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/bundles/framework/js/bootstrap.min.js"></script>
    <script src="/bundles/fosjsrouting/js/router.js"></script>

    <script src="/bundles/framework/js/main.js"></script>
    <script src="/bundles/framework/js/jquery.form.js"></script>

    <!-- messages -->
    <script src="/bundles/framework/js/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="/bundles/framework/js/jquery.matchHeight-min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

    <!--<script src="https://cdn.jsdelivr.net/jquery.ui.timepicker.addon/1.4.5/jquery-ui-timepicker-addon.min.js"></script>-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.7.2/min/dropzone.min.js"></script>
    <script>
        $(document).ready(function () {

            if ($('.datepicker').length) {
                $('.datepicker').datetimepicker({
                    controlType: 'select',
                    oneLine: true,
                    timeFormat: 'hh:mm tt',
                    minDate: 0, maxDate: "+1M +10D",
                    onSelect: function (selectedDate) {
                        $('.created_at').val(selectedDate);
                        console.log('w');
                    }
                }).hide();
            }

            if ($('.dropzone').length) {
            	$('.dropzone').each(function() {
            		var that = this;
	                Dropzone.autoDiscover = false;
    	            var myDropzone = new Dropzone(this, {
        	            url: '/media/upload',
            	        method: 'post',
            	        maxFilesize: 500,
            	        retryChunks: true,
            	        chunking: true,
                	    addRemoveLinks: true,
                    	acceptedFiles: 'image/jpeg,image/png,.mp4,.mkv',
                        accept: function (file, done) {
                            $(that).parent().show();
                            return done();
                        },
                        fallback: function () {
                            console.log('qv');
                        }, error: function () {
                            console.log('qv5');
                        }, success: function (file, response) {
                            console.log(response);
                            if (file.previewElement) {
                                file.previewElement.querySelector(".dz-progress").remove();
                                return file.previewElement.classList.add("dz-success");
                            }
                        }, complete: function (file) {
                            console.log(file);
                        }, removedfile: function(file) {
                            console.log('q');
                            var ref;
                            if (file.previewElement) {
                                if ((ref = file.previewElement) != null) {
                                    ref.parentNode.removeChild(file.previewElement);
                               }
                            }

                            $.ajax({
                                url: '<?= $view['router']->path('remove_media') ?>',
                                data: {
                                    name: file.name
                                },
                                type: "POST"
                            });

                            return this._updateMaxFilesReachedClass();
                        }
                    });
            	});
             }
        });
    </script>
    <!-- end messages -->

    <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/dialog-polyfill/0.5.0/dialog-polyfill.min.js"></script>

    <?php foreach($view['app']->javascripts as $script): ?>
    	<script defer src="<?= $script ?>"></script>
    <?php endforeach ?>

<script>
    $(document).ready(function () {

  		if ('classList' in document.createElement('div') &&
    		'querySelector' in document &&
    		'addEventListener' in window && Array.prototype.forEach) {
    		document.documentElement.classList.add('mdl-js');
	    	componentHandler.upgradeAllRegistered();
		}

        var Preloader = {
            'element_class': 'preload_ajax',
            'show': function () {
                var element = this.getElement();
                if (element == false) return false;
                element.fadeIn();
            },
            'hide': function () {
                var element = this.getElement();
                if (element == false) return false;
                if (element.is(':animated') == false) element.fadeOut(200);
                else setTimeout(function () {
                    element.fadeOut(200);
                }, 300);
            },
            'getElement': function () {
                if (this.element_class == undefined || this.element_class == '') {
                    alert('no prealoder class');
                    return (false);
                }
                var element = $('.' + this.element_class);
                if (element.length < 1) {
                    alert('No object with this class ' + this.element_class);
                    return (false);
                }
                return element;
            }
        };

        $(document).on('click', '.networks-list .action_item', function (e) {
            e.preventDefault();
            var action = $(this).data('action'),
                provider = $(this).parent().parent().prev().val();
            //console.log($(this).parent().parent().prev().val());

            if (action === 'reload') {
                window.location.href = '/post/feed/' + provider;
            } else if (action === 'remove') {
                window.location.href = '/user/provider/' + provider;
            }
        });

        $(document).on('click', '.user-status li a', function (e) {
            e.preventDefault();
            var parent = $(this).parent(),
                status = $(this).data('status');
            url = '/user/status/' + status;
            $.ajax({
                url: url,
                type: "POST",
                success: function (data) {
                    parent.addClass('active').siblings().removeClass('active');
                    $('.status').find('.' + status).addClass('active').siblings().removeClass('active');
                }, error: function (data) {
                    console.log(data);
                    console.log('error');
                }
            });
        });

        $(document).on('submit', '.upload_image', function (e) {
            e.preventDefault();

            var form = $(this),
                image = new FormData(this);

            if (form.find('input[type="file"]').val() == '') {
                console.log('Please Add Image');
                form.find('.error').text('Please Add Image').show();

                setTimeout(function () {
                    form.find('.error').empty().hide()
                }, 5000);
                return false;
            }

            Preloader.show();

            $.ajax({
                url: '<?= $view['router']->path('post_new') ?>',
                data: image,
                processData: false,
                contentType: false,
                type: "POST",
                success: function (data) {
                    if (data.success) {
                        console.log(data);
                        form.parents('.timeline_container').find('.post_content').val(form.parents('.timeline_container').find('.post_content').val() + '<img src="/bundles/framework/images/post/' + data.success + '"/>');
                        form.find('input[type=file]').val('');
                    } else {
                        console.log('error');
                        console.log(data);
                    }
                    Preloader.hide();
                }, error: function (data) {
                    console.log(data);
                    console.log('error');
                    Preloader.hide();
                }
            });

        });

        $(document).on('submit', '.company_image_form', function (e) {
            e.preventDefault();

            var form = $(this),
                image = new FormData(this);

            if (form.find('input[type="file"]').val() == '') {
                console.log('Please Add Image');
                form.find('.error').text('Please Add Image').show();

                setTimeout(function () {
                    form.find('.error').empty().hide()
                }, 5000);
                return false;
            }

            Preloader.show();

            $.ajax({
                url: '<?= $view['router']->path('company_image') ?>',
                data: image,
                processData: false,
                contentType: false,
                type: "POST",
                success: function (data) {
                    if (data.success) {
                        console.log(data);
                        $('.image_container').css({
                            "backgroundImage": "url(/bundles/framework/images/company/" + data.success + ")"
                        });
                        $('#company_logo').val(data.success);
                    } else {
                        console.log('error');
                        $('#company_logo').val('');
                        console.log(data);
                    }
                    Preloader.hide();
                }, error: function (data) {
                    console.log(data);
                    console.log('error');
                    Preloader.hide();
                }
            });
        });

        $(document).on('click', '.industry_block div', function (e) {
            e.preventDefault();

            $('#company_industry').val($(this).text());
            $('.industry_block').hide();
        });

        $(document).on('click', '.vote_btn', function (e) {
            e.preventDefault();
            var el = $(this),
                company = el.data('company'),
                url = '<?= $view['router']->path('company_vote', array('company' => 'company_id')) ?>';

            url = url.replace("company_id", company);

            $.ajax({
                url: url,
                type: "POST",
                success: function (data) {
                    if (data.message) {
                        var count = parseInt(el.find('.vote_count').text());

                        if (data.message === 'created') {
                            el.find('.vote_name').html("Voted");
                            el.find('.vote_count').text(count + 1);
                            el.addClass('last_vote').removeClass('first_vote');
                        } else if (data.message === 'removed') {
                            el.find('.vote_name').html("Vote");
                            el.find('.vote_count').text(count - 1);
                            el.addClass('first_vote').removeClass('last_vote');
                        }
                    }

                }, error: function (data) {
                    console.log(data);
                    console.log('error');
                }
            });
        });

        $(document).on('click', '.network_item .connect, .network_item .unfriend', function (e) {
            e.preventDefault();
            var el = $(this),
                connect = el.data('connect'),
                url = '<?= $view['router']->path('network_connect', array('user' => 'user_id')) ?>';

            var statusEl = $(this).parent().parent().parent().find('.user-status_current');            

            url = url.replace("user_id", connect);

            $.ajax({
                url: url,
                type: "POST",
                success: function (data) {
                    console.log(data);
                    if (data.message) {
                        if (data.message === 'Pending') {
                            el.html('Cancel request');                                                       
                        } else if (data.message === 'Connect') {
                            el.html('Add Friend');                                                        
                        }
                    }

                }, error: function (data) {

                    console.log('error');
                }
            });
        });

        $('.user-statuses ul li').on('click', function (e) {
            var status = $(this).find('span').attr('data-status');
                    $('.user-status .user-status_current').empty(); 
                    $('.user-statuses ul li').removeClass('active');
                    switch(status) {
                      case 'away':
                        $('.user-status .user-status_current').append('<i class="fa fa-clock-o" aria-hidden="true"></i>'); 
                        $('.item_away').addClass('active');
                        break;
                      case 'online':
                        $('.user-status .user-status_current').append('<i class="fa fa-check-circle" aria-hidden="true"></i>');
                        $('.item_online').addClass('active'); 
                        break;
                      case 'busy':
                        $('.user-status .user-status_current').append('<i class="fa fa-minus-circle" aria-hidden="true"></i>'); 
                        $('.item_busy').addClass('active');
                        break;
                      case 'invisible':
                        $('.user-status .user-status_current').append('<i class="fa fa-circle-o" aria-hidden="true"></i>'); 
                        $('.item_invisible').addClass('active');
                        break;    
                      default:                        
                        break;
                    }

            update_user_presense(status);
                    
            var url = '<?= $view['router']->path('user_status', array('status' => 'status_name')) ?>';

            //var statusEl = $(this).parent().parent().parent().find('.user-status_current');            

            url = url.replace("status_name", status);

            $.ajax({
                url: url,
                type: "POST",
                success: function (data) {

                }, error: function (data) {

                    console.log('error');
                }
            });
        });

        

        $(document).on('click', '.group_item .join, .group_item .leave', function (e) {
            e.preventDefault();
            var el = $(this),
                tradeland = el.data('group'),
                url = '<?= $view['router']->path('tradeland_connect', array('tradeland' => 'tradeland_id') ) ?>';

            url = url.replace("tradeland_id", tradeland);

            $.ajax({
                url: url,
                type: "POST",
                success: function (data) {
                    console.log(data);
                    if (data.message) {
                        if (data.message === 'Leave' || data.message === 'Join') {
                            el.find('span').text(data.message);
                        }
                    }
                }, error: function (data) {
                    console.log(data);
                    console.log('error');
                }
            });
        });
    });
</script>

<!--
<link href="/bundles/framework/css/lightbox.css" rel="stylesheet">
<link href="/bundles/framework/slick/slick.css" rel="stylesheet">
<link href="/bundles/framework/slick/slick-theme.css" rel="stylesheet">
<link href="/bundles/framework/css/style_lightbox.css" rel="stylesheet">
<script src="/bundles/framework/js/lightbox.js"></script>
<script src="/bundles/framework/slick/slick.min.js"></script>
<script src="/bundles/framework/js/scriptlightbox.js"></script>
<script>
    // Initiate Lightbox
    $(function () {
        $('.images_filter_block .item a').lightbox();
        //        $('.tranding_photo .item a').lightbox();
    });
</script>
-->

<script type="text/javascript">
   /* $(document).on('ready', function () {

        var device = $('body').width();

        $('.tranding_photo_slider').slick({
            nextArrow: '<div class="profitable-button profitable-button-right"></div>',
            prevArrow: '<div class="profitable-button profitable-button-left"></div>',


            dots: true,
            infinite: false,
            speed: 700,
            slidesToShow: 5,
            slidesToScroll: 1,
            variableWidth: true,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                        infinite: true,
                        dots: true
                    }
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                }

                // You can unslick at a given breakpoint now by adding:
                // settings: "unslick"
                // instead of a settings object
            ]

        });
    });*/
</script>
<!-------------------------------new files end-------------------------------------->
<?php if ($view['slots']->has("modals")): ?>
	<?= $view->render("AppBundle:Modal:media.html.twig") ?>
<?php endif ?>

   </div>

<div class="mdl-js-snackbar mdl-snackbar">
  <div class="mdl-snackbar__text"></div>
  <button class="mdl-snackbar__action" type="button"></button>
</div>

   <script async onload="onscroll_handler()" src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@12.0.0/dist/lazyload.min.js"></script>

</body>
</html>
