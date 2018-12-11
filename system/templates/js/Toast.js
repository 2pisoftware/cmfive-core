/**
 * Small Toast message handler
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
'use strict';

function Toast(message, duration) {
	this.message = message;
	this.duration = duration > 500 ? duration : 5000;
}

Toast.prototype.constructor = Toast;

Toast.prototype.show = function() {
	// Try and create the toast container if it doesn't exist
	var toaster = $('.cmfive-toast-message');
	if (toaster.length === 0) {
		$('.body').append($('<div>').addClass('cmfive-toast-message'));
	}
	toaster = $('.cmfive-toast-message');
	if (toaster.length === 0) {
		throw new Error('Could not create Toast container');
	}

	// Add the message and display
	toaster.html(this.message);

	toaster.addClass('cmfive-toast-message-appear');
	window.setTimeout(function() {
		toaster.removeClass('cmfive-toast-message-appear');
		window.setTimeout(function() {toaster.html('')}, 500);
	}, this.duration);

	// var promise = new Promise((resolve, reject) => {	
	// 	resolve();
	// }).then(() => {
		
	// })
}

// class Toast {

// 	constructor(message, duration) {
// 		this.message = message;
// 		this.duration = duration > 500 ? duration : 5000;
// 	}

// 	show() {
// 		// Try and create the toast container if it doesn't exist
// 		var toaster = $('.cmfive-toast-message');
// 		if (toaster.length === 0) {
// 			$('.body').append($('<div>').addClass('cmfive-toast-message'));
// 		}
// 		toaster = $('.cmfive-toast-message');
// 		if (toaster.length === 0) {
// 			throw new Error('Could not create Toast container');
// 		}
	
// 		// Add the message and display
// 		toaster.html(this.message);

// 		var promise = new Promise((resolve, reject) => {
// 			toaster.addClass('cmfive-toast-message-appear');
// 			resolve();
// 		}).then(() => {
// 			window.setTimeout(() => {
// 				toaster.removeClass('cmfive-toast-message-appear');
// 				window.setTimeout(() => {toaster.html('')}, 500);
// 			}, this.duration);
// 		})
		
// 	}

// }