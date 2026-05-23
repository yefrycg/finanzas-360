import './bootstrap';
import 'flowbite';
import './toast';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);
window.Chart = Chart;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
	const mobileMenuId = 'mobile-menu-2';
	const menu = document.getElementById(mobileMenuId);
	const toggleButton = document.querySelector(`[data-collapse-toggle="${mobileMenuId}"]`);

	if (!menu) {
		return;
	}

	const navLinks = Array.from(menu.querySelectorAll('a[data-nav-link]'));
	const indicator = menu.querySelector('[data-nav-indicator]');
	const navList = navLinks[0]?.closest('ul') ?? null;

	const linkToSectionId = (link) => {
		const href = link.getAttribute('href') ?? '';
		if (!href.startsWith('#')) {
			return null;
		}

		const id = href.slice(1).trim();
		return id.length ? id : null;
	};

	const sectionIds = Array.from(new Set(navLinks.map(linkToSectionId).filter(Boolean)));
	const sections = sectionIds.map((id) => document.getElementById(id)).filter(Boolean);

	const updateIndicator = () => {
		if (!indicator || !navList) {
			return;
		}

		const visibleSections = sections.filter((section) => {
			const rect = section.getBoundingClientRect();
			return rect.top < window.innerHeight * 0.8 && rect.bottom > window.innerHeight * 0.2;
		});

		const topSection = visibleSections[0] ?? null;
		const activeId = topSection?.id ?? null;

		const activeLink = navLinks.find((link) => linkToSectionId(link) === activeId);
		if (!activeLink) {
			indicator.style.width = '0px';
			indicator.style.transform = 'translateX(0px)';
			indicator.style.opacity = '0';
			return;
		}

		const listRect = navList.getBoundingClientRect();
		const linkRect = activeLink.getBoundingClientRect();
		const x = Math.max(0, linkRect.left - listRect.left);
		const width = Math.max(0, linkRect.width);

		indicator.style.width = `${width}px`;
		indicator.style.transform = `translateX(${x}px)`;
		indicator.style.opacity = '1';
	};

	const isMobileMenuOpen = () => {
		if (!toggleButton) {
			return false;
		}

		return toggleButton.getAttribute('aria-expanded') === 'true' || !menu.classList.contains('hidden');
	};

	const closeMobileMenuIfOpen = () => {
		if (!toggleButton) {
			return;
		}

		if (!isMobileMenuOpen()) {
			return;
		}

		toggleButton.click();
	};

	const updateToggleIcon = () => {
		if (!toggleButton) {
			return;
		}

		const expanded = toggleButton.getAttribute('aria-expanded') === 'true';
		const svgs = toggleButton.querySelectorAll('svg');
		const hamburgerIcon = svgs[0] ?? null;
		const closeIcon = svgs[1] ?? null;

		if (hamburgerIcon) {
			hamburgerIcon.classList.toggle('hidden', expanded);
		}

		if (closeIcon) {
			closeIcon.classList.toggle('hidden', !expanded);
		}
	};

	if (toggleButton) {
		updateToggleIcon();
		new MutationObserver(() => updateToggleIcon()).observe(toggleButton, {
			attributes: true,
			attributeFilter: ['aria-expanded'],
		});
	}

	menu.querySelectorAll('a').forEach((link) => {
		link.addEventListener('click', () => {
			closeMobileMenuIfOpen();
		});
	});

	if ('IntersectionObserver' in window && sections.length) {
		const observer = new IntersectionObserver(
			() => {
				window.requestAnimationFrame(updateIndicator);
			},
			{
				root: null,
				rootMargin: '-20% 0px -70% 0px',
				threshold: 0,
			}
		);

		sections.forEach((section) => observer.observe(section));
	}

	window.addEventListener('hashchange', () => {
		window.requestAnimationFrame(updateIndicator);
	});

	window.addEventListener('resize', () => {
		window.requestAnimationFrame(updateIndicator);
	});

	window.addEventListener('load', () => {
		window.requestAnimationFrame(updateIndicator);
	});
});