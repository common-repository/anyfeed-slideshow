<?PHP 
	if(function_exists('bloginfo')) { return false; }
	require_once(dirname(__FILE__).'/../../../wp-load.php');
?>

function anyfeed_slideshow(options) {
/*--------------------------- Global Variables -------------------------*/
var pause_rate=5000,fade_rate=1000,loading_text='Loading...',media_type='content';
var container="#anyfeed_slideshow_main",xml_url="temp.xml",c_width="100%",perm_title_bar=false;
var c_height="200px",navigation=true,title_bar=true,bgcolor='transparent',debug=false;
var total=0,loaded=0,slideshow_handle,loading_handle,slide_i=(1),started=false,target='_blank';
var titlebar,fish,url,re,counter=0,first_time=true,is_loaded=false,maximages=50;
for (var key in options) { if(eval(key+'!== undefined')) { eval(key+' = options.'+key+';'); } }

/*----------------------------------------------------------------------*/
	
	this.init = function() {
		
		// Set width and height
		jQuery(container).css({width: c_width, height: c_height});
		
		anyfeed.loading_start();
		// if(debug) { console.group("Parsing XML"); }
		jQuery.ajax({
			type: "GET",
			url: xml_url,
			dataType: "xml",
			timeout: 5000,
			success: function(xml) {
				if(navigation) { jQuery(container).html(jQuery(container).html()+'<div class="anyfeed_navigation anyfeed_slide_next" onclick="anyfeed.next();"></div><div class="anyfeed_navigation anyfeed_slide_prev" onclick="anyfeed.prev();"></div>'); }
				
				var media;
				jQuery(xml).find('description').each(function(){ media = jQuery(this).text(); return false; });
				if(media == 'true'){
// Media feed (Flikr, Yahoo, etc)
					jQuery(xml).find('item').each(function(){ if(total >= maximages) { return false; }
						var title = jQuery(this).find('title').text();
						var link = jQuery(this).find('link').text();
						// if(debug) { console.log("Trying to parse media feed."); }
						jQuery(this).find('[nodeName=media\\:'+media_type+']').each(function(){ // if(debug) { console.log("Successful- pulling image."); }
		
							url = jQuery(this).attr('url');
							// if(debug) { console.log(url); }
							if(url === null || url === "" || url === undefined) { 
								// if(debug) { console.log("Skipped."); } 
							}
							else{ anyfeed.loadImage(title, url, link); return false;}
						});
					});
				} else { 
// Regular RSS Feed	(prep)				
					// if(debug) { console.log("Trying to parse RSS feed."); }
					jQuery(xml).find('item').each(function(){ if(total >= maximages) { return false; }
						var tmpcounter = 0;
						var title = jQuery(this).find('title').text();
						var link = jQuery(this).find('link').text();
						// if(debug) { console.group("Title: " + title); }
							
// Regular RSS Feed
						// if(debug) { console.log("Attempting to find image attachments"); }
						jQuery(this).find('enclosure').each(function(){ 
							// if(debug) { console.info("Feed with image attachments"); }
							tmpcounter += 1;
		
							url = jQuery(this).attr('url');
							// if(debug) { console.log(url); }
							if(url === null || url === "" || url === undefined) { 
								// if(debug) { console.log("Skipped."); } 
							}
							else{ anyfeed.loadImage(title, url, link); }
						});
						

// Feed without image attachments
						if(tmpcounter === 0) { 
							// if(debug) { console.log("Attempting to find XML Content:encoded..."); }
							fish = jQuery(this).find('[nodeName=content\\:encoded]').text();
							if(fish) {
								url = jQuery(fish).find('img').attr('src');
							if(url === null || url === "" || url === undefined) { 
								// if(debug) { console.log("Skipped."); } 
							}
								else{
									// if(debug) { console.info("Feed without image attachments"); }
									// if(debug) { console.log("URL: " + url); }
									tmpcounter += 1;
									anyfeed.loadImage(title, url, link);
								}
							} // end check fish for content
						} // End feed without image attachments
						
// Feed without Content, only descriptions
						if(tmpcounter === 0) { 
							// if(debug) { console.log("Attempting to find images in description text..."); }
							fish = jQuery(this).find('description').text();
							if(jQuery(fish).find('img').each(function(){return true;})) {
								url = jQuery(fish).find('img').attr('src');
								// This gets rid of comment counts and some advertisements
								re = new RegExp("/(.*feedburner.*|.*ads\..*)/", "i");
								m = re.exec(url);
								if (m===null && url!==undefined) {
									// if(debug) { console.info("Feed without content "); }
									// if(debug) { console.log("URL: " + url); }
									tmpcounter + 1;
									anyfeed.loadImage(title, url, link);
								}  else { // if(debug) { console.log("skipped."); } 
								}
							}  else { // if(debug) { console.log("Nothing here! Skipping."); } 
							}
							
						} // End feed without content
					// if(debug) { console.groupEnd(); }
						
					});
				}
/****************** entry *******************************/
				if(counter === 0) {
				// if(debug) { console.log("Trying to parse RSS feed."); }
				jQuery(xml).find('entry').each(function(){ if(total >= maximages) { return false; }
					var tmpcounter = 0;
					var title = jQuery(this).find('title').text();
					var link = jQuery(this).find('link').text();
					// if(debug) { console.group("Title: " + title); }
					// if(debug) { console.log("Attempting to find image attachments"); }
					jQuery(this).find('link').each(function(){ 
						if(jQuery(this).attr('rel') == 'enclosure') { 
							// if(debug) { console.info("Feed with image attachments"); }
							tmpcounter += 1;
		
							url = jQuery(this).attr('href');
							// if(debug) { console.log(url); }
							if(url === null || url === "" || url === undefined) { 
								// if(debug) { console.log("Skipped."); } 
							}
							else{ anyfeed.loadImage(title, url, link); }
						}
					});

				
					
					// if(debug) { console.groupEnd(); }
			});
		} // End if Counter ===0 
				
				if(counter === 0) {
				
				
				
					title = 'No images found'
					link = 'http://tixen.net'
					anyfeed.loadImage(title, 'http://tixen.net/files/anyfeed-slideshow/error.gif', link);
				}

				jQuery(container).hover(
					function() { 
						if(is_loaded){
							if(!perm_title_bar) {
								jQuery("div.anyfeed_titlebar").fadeIn(fade_rate / 5);
							}
							if(loaded > 1)jQuery("div.anyfeed_navigation").fadeIn(fade_rate / 5);
							setTimeout(function() {jQuery("div.anyfeed_titlebar").css({display: "block"});},fade_rate / 3);
							if(loaded > 1)setTimeout(function() {jQuery("div.anyfeed_navigation").css({display: "block"});},fade_rate / 3);
						}
						started = false;
						clearTimeout(slideshow_handle); 
					}, function () { 
						if(!perm_title_bar) {
							jQuery("div.anyfeed_titlebar").fadeOut(fade_rate / 2);
						}
						if(loaded > 1) jQuery("div.anyfeed_navigation").fadeOut(fade_rate / 2);
						setTimeout(function() {jQuery("div.anyfeed_titlebar").css({display: "none"});},fade_rate / 2);
						anyfeed.start();
					}
				);
			
	// if(debug) { console.groupEnd(); }
	// if(debug) { console.group("Loading Images..."); }
	
			},
			error: function() {
				// if(debug){jQuery(container).html("Failed to retreive XML file."); }
			}
		});
	}
	
/******************************************************************************** START */
	this.start = function() {

		// if(debug) { console.groupEnd(); }
		if(!started){
			if(first_time) {
				anyfeed.loading_stop(1);
				is_loaded=true;
				jQuery("#anyfeed_photo_1").fadeIn(fade_rate);
				if(total == 1){return false;}
				first_time = false;
			}
			started = true; 
			slideshow_handle = setInterval(function() {
				if(slide_i >= total){
					var fadethis="#anyfeed_photo_"+(total);
					slide_i = 1; 
				} else { 
					var fadethis = "#anyfeed_photo_"+(slide_i);
					slide_i = slide_i + 1;
				}	
				
				// if(debug){ console.log("Photo "+ slide_i + "/" +total);}
				setTimeout(function(){
					jQuery(fadethis).fadeOut(fade_rate);
				}, fade_rate / 2);
				jQuery("#anyfeed_photo_"+slide_i).fadeIn(fade_rate);
				
			}, pause_rate + fade_rate);
		}
	}
	
/********************************************************************************* NEXT */
	this.next = function(){
			if(slide_i >= total){
				var fadethis="#anyfeed_photo_"+(total);  
				slide_i = 1; 
			} else { 
				var fadethis="#anyfeed_photo_"+(slide_i);
				slide_i = slide_i + 1;
			}
		setTimeout(function(){
			// if(debug){ console.log("Photo "+ slide_i + "/" +total);}
			jQuery(fadethis).fadeOut(fade_rate / 3);
		}, fade_rate / 6);
		jQuery("#anyfeed_photo_"+slide_i).fadeIn(fade_rate / 3);
	}	
	
/********************************************************************************* PREV */
	this.prev = function(){
		if(slide_i <= 1){
			var fadethis="#anyfeed_photo_"+total; 
			slide_i = total; 
		}else {
			var fadethis="#anyfeed_photo_"+slide_i;
			slide_i = slide_i - 1;
		}
		setTimeout(function(){
			// if(debug){ console.log("Photo "+ slide_i + "/" +total);}
			jQuery(fadethis).fadeOut(fade_rate / 3);
		}, fade_rate / 6);
		jQuery("#anyfeed_photo_"+slide_i).fadeIn(fade_rate / 3);
	}

/************************************************************************ LOADING_START */
	this.loading_start = function() {
		jQuery(container).html('<div id="anyfeed_slideshow_loading">'+loading_text+'</div>');
		jQuery('#anyfeed_slideshow_loading').css({opacity: 0.1});
		loading_handle = setInterval(function() {
			jQuery("#anyfeed_slideshow_loading").animate( { opacity: 0.5	}, 1400, function() {
				jQuery("#anyfeed_slideshow_loading").animate( { opacity: 0.1	}, 1400);
			});
		}, 3000); // End setInterval
	} // End loading_start
		
			
/************************************************************************* LOADING_STOP */
	this.loading_stop = function(when) {
		if(started) {return false;}
		setTimeout(function(){
			clearTimeout(loading_handle);
			jQuery('#anyfeed_slideshow_loading')
				.stop(true)
				.fadeOut(100, 'swing', function() {
					document.getElementById("anyfeed_slideshow_loading").style.display = "none";
					is_loaded = true;
				});
			
		}, when);
	} // End loading_stop
	
/**************************************************************************** LOADIMAGE */
	this.loadImage = function(title, url, link) { if(total >= maximages) { return false; }
		counter = counter + 1;
		total = total + 1;
		var i1 = new Image();
		i1.onload = function() {
			loaded = loaded + 1;
			if(perm_title_bar) {var h = ' style="display: block !important;" '; } else {var h = ''; }
			if(title_bar){ titlebar='<div class="anyfeed_titlebar"'+h+'>'+title+'</div>';} else{ titlebar = ''; }
			jQuery(container).html(jQuery(container).html()+'<div class="anyfeed_photo" id="anyfeed_photo_'+loaded+'" style="background-image: url(\''+url+'\'); background-color: '+bgcolor+';" onclick="window.open(\''+link+'\', \''+target+'\');">'+titlebar+'</div>');
			// if(debug) { console.log("Added."); }
			if(loaded == total){ 
				anyfeed.start();
			}
		};
		i1.onabort = function() {
			total = total - 1;
			// if(debug) { console.log('Failed to download image.'); }
			
		};
		i1.onerror = function() {
			total = total - 1;
			// if(debug) { console.log('Failed to download image.'); }
			if(total == 0) {
				titlebar='<div class="anyfeed_titlebar">Images failed to load</div>';
				jQuery(container).html(jQuery(container).html()+'<div class="anyfeed_photo" id="anyfeed_photo_1" style="background-image: url(\'http://tixen.net/files/anyfeed-slideshow/error.gif\'); background-color: '+bgcolor+';" onclick="window.open(\'#\');">'+titlebar+'</div>');
				total=1; loaded=1;
				anyfeed.start();
			}
		};
		i1.src = url;
	}
	
/********************************************************************************** DIE */
	this.die = function() {
		started = false;
		clearTimeout(slideshow_handle); 
	}


/**************************************************************************** GETDATA */	
	this.getData = function() { 
		alert(
			('loaded: '+loaded)+"\r\n"+ 
			('total: '+total)+"\r\n"+
			('maximages: '+maximages)+"\r\n"+
			('counter: '+counter)+"\r\n"+
			('started: '+started)
		);
	}

} // End Class function





var anyfeed;
jQuery(document).ready(function(){ anyfeed = new anyfeed_slideshow({
		pause_rate: <?php echo get_option('anyfeed_pause_rate');?>,
		fade_rate: <?php echo get_option('anyfeed_fade_rate');?>,
		media_type: '<?php echo get_option('anyfeed_media_type');?>',
		loading_text: '<?php echo addslashes(get_option('anyfeed_loading_text'));?>',
		title_bar: <?php if(get_option('anyfeed_show_titlebar')) echo 'true'; else echo 'false';?>,
		perm_title_bar: <?php if(get_option('anyfeed_perm_titlebar')) echo 'true'; else echo 'false';?>,
		navigation: <?php if(get_option('anyfeed_show_navigation')) echo 'true'; else echo 'false';?>,
		c_width: '<?php echo get_option('anyfeed_width');?>',
		c_height: '<?php echo get_option('anyfeed_bgcolor');?>',
		c_height: '<?php echo get_option('anyfeed_height');?>',
		bgcolor: '<?php echo get_option('anyfeed_bgcolor');?>',
		target: '<?php echo get_option('anyfeed_target');?>',
		xml_url: '<?php echo anyfeed_pathTo().'/anyfeed_slideshow.php?xml'; ?>',
		maximages: '<?php echo get_option('anyfeed_maximages');?>',
		debug: false
}); anyfeed.init(); });
