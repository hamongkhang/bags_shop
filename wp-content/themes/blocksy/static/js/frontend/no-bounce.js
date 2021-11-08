import {
	clearAllBodyScrollLocks,
	enableBodyScroll,
	disableBodyScroll,
} from 'body-scroll-lock'

export var enable = function (el) {
	clearAllBodyScrollLocks()
	// enableBodyScroll(el, { reserveScrollBarGap: true })
}

export var disable = function (el) {
	disableBodyScroll(el, {
		reserveScrollBarGap: true,

		allowTouchMove: (el) => {
			if (el.closest('.select2-container')) {
				return true
			}

			return false
		},
	})
}
