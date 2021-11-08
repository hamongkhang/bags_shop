export const clamp = (min, max, value) => Math.max(min, Math.min(max, value))

export const computeLinearScale = (domain, range, value) =>
	range[0] +
	((range[1] - range[0]) / (domain[1] - domain[0])) * (value - domain[0])

export const getRowInitialHeight = (el) => {
	if (el.blcInitialHeight) {
		return el.blcInitialHeight
	}

	const elComp = getComputedStyle(el)
	const containerComp = getComputedStyle(el.firstElementChild)

	let border = 0

	border += parseFloat(elComp.borderTopWidth)
	border += parseFloat(elComp.borderBottomWidth)

	border += parseFloat(containerComp.borderTopWidth)
	border += parseFloat(containerComp.borderBottomWidth)

	const initialHeight =
		parseFloat(elComp.getPropertyValue('--height')) + border

	el.blcInitialHeight = initialHeight

	return initialHeight
}

export const getRowStickyHeight = (el) => {
	let styles = getComputedStyle(el)

	let maybeShrink = styles.getPropertyValue('--sticky-shrink')

	if (!maybeShrink) {
		return getRowInitialHeight(el)
	}

	return (parseFloat(maybeShrink) / 100) * getRowInitialHeight(el)
}
