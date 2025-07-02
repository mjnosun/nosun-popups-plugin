jQuery(document).ready(function($) {
	/*
	VARIABLES
	--------------------------------------------------------- */
	const $window = $(window);
	const $document = $(document);
	const $body = $("body");
	const activeClass = "active";
	const currentLanguage = $("html").attr("lang");

	// Common functions for both AJAX and default behavior
	function getPopupSeen(id, language, storage) {
		var item = "nosun_popup_" + id + "_lang-" + language + "_seen";
		var seen = false;
		switch (storage) {
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
		var item = "nosun_popup_" + id + "_lang-" + language + "_seen";
		switch (storage) {
			case "session":
				sessionStorage.setItem(item, "true");
				break;
			case "local":
				localStorage.setItem(item, "true");
				break;
			case "none":
			default:
				break;
		}
	}

	// Popup logic, applicable regardless of AJAX or default behavior
	function initializePopup($popupWrapper) {
		const popupID = $popupWrapper.attr("id").substr(6);
		const triggerType = $popupWrapper.data("trigger-type");
		const storage = $popupWrapper.data('storage');

		// Set popup elements & adjust popup position if smaller than screen
		let popupElements;
		if ($popupWrapper.hasClass("popup-style-default")) {
			popupElements = $(".popup-bg#popup-bg-" + popupID + ", .popup-wrapper#popup-" + popupID);
			if ($popupWrapper.find(".popup").outerHeight() >= $window.height()) {
				$popupWrapper.css({
					'margin-top': '5px',
					'align-items': 'flex-start',
				});
			}
		} else {
			popupElements = $(".popup-wrapper#popup-" + popupID);
		}

		// Check session & trigger visibility
		if (!getPopupSeen(popupID, currentLanguage, storage)) {
			if (triggerType === 'time-delay') {
				var timeDelay = parseInt($popupWrapper.data("time-delay"));
				setTimeout(function() {
					popupElements.addClass(activeClass);
				}, timeDelay);
			} else if (triggerType === 'scroll-amount') {
				var scrollAmountTrigger = parseInt($popupWrapper.data("scroll-amount"));
				var alreadyTriggered = false;
				$window.scroll(function() {
					var scrollAmount = $window.scrollTop();
					var documentHeight = $document.height();
					var scrollPercent = (scrollAmount / documentHeight) * 100;
					if (scrollPercent >= scrollAmountTrigger && !alreadyTriggered) {
						popupElements.addClass(activeClass);
						alreadyTriggered = true;
					}
				});
			} else if (triggerType === 'click') {
				var clickElementsTrigger = $($popupWrapper.data("clicked-elements"));
				$document.on("click", clickElementsTrigger,function(e) {
					popupElements.addClass(activeClass);
				});
			} else if (triggerType === 'element-visible') {
				var visibleElementTrigger = $($popupWrapper.data("visible-element"));
				if (visibleElementTrigger.length) {
					var el = document.getElementById(visibleElementTrigger.attr('id'));
					var windowHeightHalf = $window.height() / 2;
					var alreadyTriggered = false;
					$window.scroll(function() {
						var elPos = el.getBoundingClientRect();
						var scrollAmount = $window.scrollTop() + windowHeightHalf;
						if (elPos.top <= windowHeightHalf && !alreadyTriggered) {
							popupElements.addClass(activeClass);
							alreadyTriggered = true;
						}
					});
				}
			}
		}
	}

	// Regular behavior to show popup after body is opened
	$(".popup-wrapper").each(function() {
		initializePopup($(this)); // Reinitialize popups on page load
	});

	// Handle close logic and focus trapping (same as your original code)
	$document.on("click", ".popup-close, .popup-bg", function(e) {
	// $(".popup-close, .popup-bg").click(function(e) {
		e.preventDefault();
		var targetPopup;
		if ($(this).hasClass("popup-bg")) {
			targetPopup = $(this).next(".popup-wrapper");
			$(this).removeClass(activeClass);
		} else {
			targetPopup = $(this).closest(".popup-wrapper");
			if (targetPopup.hasClass("popup-style-default")) {
				targetPopup.prev(".popup-bg").removeClass(activeClass);
			}
		}
		targetPopup.removeClass(activeClass);
		
		var popupID = targetPopup.attr("id").substr(6);
		var storage = targetPopup.data("storage");
		setPopupSeen(popupID, currentLanguage, storage);
	});

	$document.on("click", ".popup-content-wrap a", function(e) {
	// $(".popup-content-wrap a").click(function(e) {
		var targetPopup = $(this).closest(".popup-wrapper");
		if (targetPopup.hasClass("popup-style-default")) {
			targetPopup.prev(".popup-bg").removeClass(activeClass);
		}
		targetPopup.removeClass(activeClass);
		var popupID = targetPopup.attr("id").substr(6);
		var storage = targetPopup.data("storage");
		setPopupSeen(popupID, currentLanguage, storage);
	});

	// Show popup again button on single popup page
	$document.on("click", ".show-popup-again",function(e) {
	// $(".show-popup-again").click(function(e) {
		e.preventDefault();
		$(".popup-bg, .popup-wrapper").addClass(activeClass);
		return false;
	});
	
});
