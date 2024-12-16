jQuery(document).ready(function($){
	/*
	VARIABLES
	--------------------------------------------------------- */
	const $window = $(window);
	const $document = $(document);
	const $body = $("body");
	const activeClass = "active";
	const currentLanguage = $("html").attr("lang");
	function getPopupSeen(id, language, storage) {
		var item = "nosun_popup_"+id+"_lang-"+language+"_seen";
		var seen = false;
		switch ( storage ) {
			case "session":
				seen = sessionStorage.getItem(item);
				break;
			case "local":
				seen = localStorage.getItem(item);
				break;
			case "none":
			default:
				seen = false;
				break;
		}
		return seen;
	}
	function setPopupSeen(id, language, storage) {
		console.log(id + " " + language + " " + storage);
		var item = "nosun_popup_"+id+"_lang-"+language+"_seen";
		switch ( storage ) {
			case "session":
				seen = sessionStorage.setItem(item, "true");
				break;
			case "local":
				seen = localStorage.setItem(item, "true");
				break;
			case "none":
			default:
				break;
		}
	}

	/* 
	show popup
	and flex position fix
	--------------------------------------------------------- */
	if ( $(".popup-wrapper").length ) {
		$(".popup-wrapper").each(function(){
			var $this = $(this);
			const popupID = $this.attr("id").substr(6);
			const triggerType = $this.data("trigger-type");
			const storage = $(this).data('storage');
			
			/*
			set popup elements
			& adjust popup position if smaller than screen
			--------------------------------------------------------- */
			if ( $this.hasClass("popup-style-default") ) {
				var popupElements = $(".popup-bg#popup-bg-"+popupID+", .popup-wrapper#popup-"+popupID+"");
				if ( $this.find(".popup").outerHeight() >= $window.height() ) {
					$this.css({
						'margin-top': '5px',
						'align-items': 'flex-start',			
					});
				}
			} else {
				var popupElements = $(".popup-wrapper#popup-"+popupID+"");
			}
			
			/*
			check if session exists & trigger visibility
			--------------------------------------------------------- */
			if ( !getPopupSeen(popupID, currentLanguage, storage) ) {
				// triggerType: time-delay
				if ( triggerType == 'time-delay' ) {
					var timeDelay = parseInt($this.data("time-delay"));
					setTimeout(function(){
						popupElements.addClass(activeClass);
					}, timeDelay);		
				}
				// triggerType: scroll-amount
				else if ( triggerType == 'scroll-amount' ) {
					var scrollAmountTrigger = parseInt($this.data("scroll-amount"));
					var alreadyTriggered = false;
					$window.scroll(function() {
						var scrollAmount = $window.scrollTop();
						var documentHeight = $document.height();
						var scrollPercent = (scrollAmount / documentHeight) * 100;
						if ( scrollPercent >= scrollAmountTrigger && alreadyTriggered == false ) {
							popupElements.addClass(activeClass);
							alreadyTriggered = true;
						}
						// else {
						// 	popupElements.removeClass(activeClass);
						// }
					});
				}
				// triggerType: click
				else if ( triggerType == 'click' ) {
					var clickElementsTrigger = $(""+$this.data("clicked-elements")+"");
					clickElementsTrigger.click(function(e){
						popupElements.addClass(activeClass);
					});
				}
				// triggerType: element-visible
				else if ( triggerType == 'element-visible' ) {
					var visibleElementTrigger = $($this.data("visible-element"));
					if ( visibleElementTrigger.length ) {
						var elID = $this.data("visible-element").substr(1);
						var el = document.getElementById(elID);
						var windowHeightHalf = $window.height() / 2;
						var alreadyTriggered = false;
						$window.scroll(function() {
							var elPos = el.getBoundingClientRect();
							var scrollAmount = $window.scrollTop() + windowHeightHalf;
							if ( elPos.top <= windowHeightHalf && alreadyTriggered == false ) {
								popupElements.addClass(activeClass);
								alreadyTriggered = true;
							}
						});
					}
				}				
			}
		});
	}	

	/*
	focus to first/last focusable element when popup is active
	--------------------------------------------------------- */
	$(".popup-wrapper").on("transitionend focus-inside", function(e, index) {
		var $this = $(this);
		if (e.target == this && $this.hasClass(activeClass)) {
			setTimeout(function() {
				var $focusable = $this.children(".popup").find('input, select, textarea, button, object, a, area[href], [tabindex]');
				if (index == 'last') {
					$focusable.last().focus();
				}
				else {
					$focusable.first().focus();
				}
			});
		}
	});

	/*
	close popup when pressing escape
	--------------------------------------------------------- */
    $(document).on("keydown", function(e) {
        if (e.key == "Escape") {
            if ($(".popup-wrapper").hasClass(activeClass)) {
				$(".popup-close").click();
            }
        }
    });

	/*
	trap the focus to the active popup
	--------------------------------------------------------- */
	$(".trap-focus").on("focus", function() {
		$(".popup-wrapper").trigger("focus-inside", $(this).index() == 0 ? 'last' : 'first');
	});

	/* 
	close popup and set session
	--------------------------------------------------------- */
	$(".popup-close, .popup-bg").click(function(e){
		e.preventDefault();
		if ( $(this).hasClass("popup-bg") ) {
			var targetPopup = $(this).next(".popup-wrapper");
			$(this).removeClass(activeClass);
		} else {
			var targetPopup = $(this).closest(".popup-wrapper");
			if ( targetPopup.hasClass("popup-style-default") ) {
				targetPopup.prev(".popup-bg").removeClass(activeClass);
			}
		}
		targetPopup.removeClass(activeClass);
		var popupID = targetPopup.attr("id").substr(6);
		var storage = targetPopup.data("storage");
		setPopupSeen(popupID, currentLanguage, storage);
	});
	$(".popup-content-wrap a").click(function(e){
		var targetPopup = $(this).closest(".popup-wrapper");
		if ( targetPopup.hasClass("popup-style-default") ) {
			targetPopup.prev(".popup-bg").removeClass(activeClass);
		}
		targetPopup.removeClass(activeClass);
		var popupID = targetPopup.attr("id").substr(6);
		var storage = targetPopup.data("storage");
		setPopupSeen(popupID, currentLanguage, storage);
	});
	
	/* 
	show popup again button on single popup page
	--------------------------------------------------------- */
	$(".show-popup-again").click(function(e){
		e.preventDefault();
		$(".popup-bg, .popup-wrapper").addClass(activeClass);
		return false;
	});
	
	
});
