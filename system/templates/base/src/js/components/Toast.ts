/**
 * Small Toast message handler
 *
 * @author Adam Buckley <adam@2pisoftware.com>
 */
'use strict';

export class Toast
{
	public message: string;
	public duration: number;

	private static toastParentElement: string = 'body';
	private static messageTargetClass: string = 'cmfive-toast-message';
	private static toastAppearClass: string = 'cmfive-toast-message-appear';

	public constructor(message: string, duration: number = 5000)
	{
		this.message = message;
		this.duration = duration > 500 ? duration : 5000;
	}

	public show()
	{
		let toaster = document.querySelector('.' + Toast.messageTargetClass);
		if (!toaster) {
			let toaster = document.createElement('div');
			toaster.classList.add(Toast.messageTargetClass);
			document.querySelector(Toast.toastParentElement).appendChild(toaster);
		}

		toaster = document.querySelector('.' + Toast.messageTargetClass)
		if (!toaster) {
			throw new Error('Could not create Toaster element');
		}
		
		// Add the message and display
		toaster.innerHTML = this.message;

		toaster.classList.add(Toast.toastAppearClass);
		window.setTimeout(function() {
			toaster.classList.remove(Toast.toastAppearClass);
			window.setTimeout(() => toaster.innerHTML = '', 500);
		}, this.duration);
	}
}

