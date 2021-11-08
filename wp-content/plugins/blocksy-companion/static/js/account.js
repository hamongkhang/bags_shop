import ctEvents from 'ct-events'
import { registerDynamicChunk } from 'blocksy-frontend'
import { handleAccountModal, activateScreen } from './frontend/account'

const ensureAccountModalPresent = (cb) => {
	const selector = '#account-modal'
	try {
		document.querySelector(selector)
	} catch (e) {
		return
	}

	if (document.querySelector(selector)) {
		cb(document.querySelector(selector))
		return
	}

	fetch(
		`${ct_localizations.ajax_url}?action=blc_retrieve_account_modal`,

		{
			method: 'POST',
			body: JSON.stringify({
				current_url: location.href,
			}),
		}
	)
		.then((response) => response.json())
		.then(({ data: { html } }) => {
			const drawerCanvas = document.querySelector('.ct-drawer-canvas')
			drawerCanvas.insertAdjacentHTML('beforeend', html)

			setTimeout(() => {
				cb(document.querySelector(selector))
			})
		})
}

registerDynamicChunk('blocksy_account', {
	mount: (el, { event }) => {
		event.preventDefault()

		ensureAccountModalPresent((accountModal) => {
			handleAccountModal(accountModal)

			activateScreen(accountModal, {
				screen: el.dataset.view || 'login',
			})

			ctEvents.trigger('ct:overlay:handle-click', {
				e: event,
				href: '#account-modal',
				options: {
					isModal: true,
				},
			})
		})
	},
})
