import ctEvents from 'ct-events'
import { getCurrentScreen } from 'blocksy-frontend'

import { computeShrink } from './sticky/shrink'
import { computeAutoHide } from './sticky/auto-hide'
import { computeFadeSlide } from './sticky/fade-slide'

export const setTransparencyFor = (deviceContainer, value = 'yes') => {
	Array.from(
		deviceContainer.querySelectorAll('[data-row][data-transparent-row]')
	).map((el) => {
		el.dataset.transparentRow = value
	})
}

var getParents = function (elem) {
	var parents = []

	for (; elem && elem !== document; elem = elem.parentNode) {
		parents.push(elem)
	}

	return parents
}

let cachedStartPosition = null
let cachedContainerInitialHeight = null

let cachedStickyContainerHeight = null

const getStartPositionFor = (stickyContainer) => {
	if (
		stickyContainer.dataset.sticky.indexOf('shrink') === -1 &&
		stickyContainer.dataset.sticky.indexOf('auto-hide') === -1
	) {
		return stickyContainer.parentNode.getBoundingClientRect().height + 200
	}

	const headerRect = stickyContainer.closest('header').getBoundingClientRect()

	let stickyOffset = headerRect.top + scrollY

	if (stickyOffset > 0) {
		let element = document.elementFromPoint(
			0,
			stickyContainer.getBoundingClientRect().top - 3
		)

		if (element) {
			if (
				getParents(element)
					.map((el) => {
						let style = getComputedStyle(el)
						return style.position
					})
					.indexOf('fixed') > -1
			) {
				stickyOffset = 0
			}
		}
	}

	const row = stickyContainer.parentNode

	if (
		row.parentNode.children.length === 1 ||
		row.parentNode.children[0].classList.contains('ct-sticky-container')
	) {
		return stickyOffset
	}

	return Array.from(row.parentNode.children)
		.reduce((result, el, index) => {
			if (result.indexOf(0) > -1 || !el.dataset.row) {
				return [...result, 0]
			} else {
				return [
					...result,

					el.classList.contains('ct-sticky-container')
						? 0
						: el.getBoundingClientRect().height,
				]
			}
		}, [])
		.reduce((sum, height) => sum + height, stickyOffset)
}

let prevScrollY = null

const compute = () => {
	if (prevScrollY === scrollY) {
		/*
		requestAnimationFrame(() => {
			compute()
		})
    */

		return
	}

	prevScrollY = scrollY

	const stickyContainer = document.querySelector(
		`[data-device="${getCurrentScreen()}"] [data-sticky]`
	)

	if (!stickyContainer) {
		return
	}

	let startPosition = cachedStartPosition

	if (!startPosition) {
		startPosition = getStartPositionFor(stickyContainer)
	}

	let stickyContainerHeight = cachedStickyContainerHeight

	if (!stickyContainerHeight) {
		stickyContainerHeight = parseInt(
			stickyContainer.getBoundingClientRect().height
		)
		cachedStickyContainerHeight = parseInt(stickyContainerHeight)
	}

	const stickyComponents = stickyContainer.dataset.sticky
		.split(':')
		.filter((c) => c !== 'yes' && c !== 'no' && c !== 'fixed')

	let isSticky =
		(startPosition > 0 && Math.abs(window.scrollY - startPosition) < 5) ||
		window.scrollY > startPosition

	if (stickyComponents.indexOf('shrink') > -1) {
		isSticky =
			startPosition > 0
				? window.scrollY >= startPosition
				: window.scrollY > 0
	}

	setTimeout(() => {
		if (isSticky && document.body.dataset.header.indexOf('shrink') === -1) {
			document.body.dataset.header = `${document.body.dataset.header}:shrink`
		}

		if (!isSticky && document.body.dataset.header.indexOf('shrink') > -1) {
			document.body.dataset.header = document.body.dataset.header.replace(
				':shrink',
				''
			)
		}
	}, 300)

	let containerInitialHeight = cachedContainerInitialHeight

	if (!containerInitialHeight) {
		cachedContainerInitialHeight = Array.from(
			stickyContainer.querySelectorAll('[data-row]')
		).reduce((sum, el) => sum + el.getBoundingClientRect().height, 0)

		containerInitialHeight = cachedContainerInitialHeight

		stickyContainer.parentNode.style.height = `${containerInitialHeight}px`
	}

	if (stickyComponents.indexOf('shrink') > -1) {
		computeShrink({
			stickyContainer,
			stickyContainerHeight,

			containerInitialHeight,
			isSticky,
			startPosition,
			stickyComponents,
		})
	}

	if (stickyComponents.indexOf('auto-hide') > -1) {
		computeAutoHide({
			stickyContainer,
			isSticky,
			startPosition,
			stickyComponents,
		})
	}

	if (
		stickyComponents.indexOf('slide') > -1 ||
		stickyComponents.indexOf('fade') > -1
	) {
		computeFadeSlide({
			stickyContainer,
			isSticky,
			startPosition,
			stickyComponents,
		})
	}
}

export const mountStickyHeader = () => {
	if (!document.querySelector('header [data-sticky]')) {
		return
	}

	window.addEventListener('resize', compute, false)
	window.addEventListener('scroll', compute, false)
	window.addEventListener('load', compute, false)
	window.addEventListener('orientationchange', compute)

	compute()
}
