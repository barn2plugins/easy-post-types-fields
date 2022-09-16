/**
 * External dependencies
 */
import { addFilter } from '@wordpress/hooks';
import { sprintf, __ } from '@wordpress/i18n';
import { isEmpty } from 'lodash';

/**
 * Internal dependencies
 */
import WelcomeStep from './WelcomeStep';

/**
 * Override steps components.
 */
addFilter('barn2_setup_wizard_steps', 'ept-wizard', (steps) => {
	steps[0].component = WelcomeStep

	const query = new URLSearchParams(location.search);

	// Remove the "welcome step".
	if ( query.has('action') ) {
		return steps.filter( function( step ) {
			return step.key !== 'welcome_free' && step.key !== 'more'
		})
	}

	return steps;
});

/**
 * Listen to value changes into the setup wizard
 * and adjust the text of the steps.
 */
window.addEventListener( 'barn2_setup_wizard_changed', (dispatchedEvent) => {
	// Method used to set overrides.
	const setStepOverride = dispatchedEvent.detail.setStepOverride

	// Value that we're looking for.
	const value = dispatchedEvent.detail.plural

	if ( ! isEmpty( value ) ) {
		const featuresStep = barn2_setup_wizard.steps.find( step => step.key === 'ept_features' )
		const readyStep = barn2_setup_wizard.steps.find( step => step.key === 'ept_ready' )

		setStepOverride( 'ept_features', {
			pageTitle: sprintf( featuresStep.heading, value ),
			pageDescription: sprintf( featuresStep.description, value )
		} )

		setStepOverride( 'ept_ready', {
			pageTitle: sprintf( readyStep.heading, value ),
			pageDescription: sprintf( readyStep.description, value )
		} )
	}
}, false);

/**
 * Adjust the label of the skip link.
 */
addFilter( 'barn2_setup_wizard_skip_label', 'ept-wizard', ( label ) => {

	const query = new URLSearchParams(location.search);

	if ( query.has('action') ) {
		return __( 'Cancel' );
	}

	return label;

} );

/**
 * Adjust the labels of the "next" step button.
 */
addFilter( 'barn2_setup_wizard_next_button_label', 'ept-wizard', ( label, activeStep ) => {

	const query = new URLSearchParams(location.search)

	if ( query.has('action') ) {
		if ( 'ept_name' === activeStep ) {
			return __( 'Next' )
		}

		if ( 'ept_features' === activeStep ) {
			return __( 'Create' )
		}
	}

	return label;

} );
