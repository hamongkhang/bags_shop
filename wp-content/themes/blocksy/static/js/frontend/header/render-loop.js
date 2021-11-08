import ctEvents from 'ct-events'

let prevInnerWidth = null

const renderHeader = () => {
	if (!prevInnerWidth || window.innerWidth !== prevInnerWidth) {
		prevInnerWidth = window.innerWidth
		ctEvents.trigger('ct:header:render-frame')
	}

	requestAnimationFrame(renderHeader)
}

export const mountRenderHeaderLoop = () => {
	if (window.wp && wp && wp.customize && wp.customize.selectiveRefresh) {
		wp.customize.selectiveRefresh.bind('partial-content-rendered', (e) => {
			ctEvents.trigger('ct:header:update')
			ctEvents.trigger('ct:header:render-frame')
		})
	}

	requestAnimationFrame(renderHeader)
}
