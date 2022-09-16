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

} )

/*
import { __, sprintf } from '@wordpress/i18n';
import { addAction, addFilter } from '@wordpress/hooks';

import Welcome from './welcome';
import EPT_Ready from './ready';

const addCustomSteps = ( steps ) => {
	const customSteps = [
		{ key: 'welcome', component: Welcome },
		{ key: 'ready', component: EPT_Ready },
	];

	for ( const { key, component } of customSteps ) {
		const index = steps.indexOf( steps.find( s => key === s.key ) );
		if ( -1 !== index ) {
			steps[ index ].container = component;
		}
	}

	return steps;
}
addFilter( 'barn2_setup_wizard_steps', 'ept_post_types', addCustomSteps );

const onStepHeaderUpdated = ( header ) => {
	if ( -1 !== [ 'ept_features', 'ept_ready' ].indexOf( header.props.step.key ) ) {
		const plural      = header.props.stepProps.getValues().plural,
			  heading     = sprintf( header.props.step.heading, plural ),
			  description = sprintf( header.props.step.description, plural );

		header.setState({ heading, description })
	}
}
addAction( 'barn2_wizard_step_header_mounted', 'ept_post_types', onStepHeaderUpdated );
addAction( 'barn2_wizard_step_header_updated', 'ept_post_types', onStepHeaderUpdated );

const onFooterUpdated = ( footer ) => {
	const query = new URLSearchParams(location.search)

	if ( query.has('action') ) {
		if ( -1 !== [ 'ept_name', 'ept_features' ].indexOf( footer.props.step.key ) ) {
			footer.setState( { isSkipTooltipVisible: false, skipText: __( 'Cancel' ) } )
		}
	
		if ( 'ept_ready' === footer.props.step.key ) {
			footer.setState( { isSkipVisible: false } )
		}
	}

}
addAction( 'barn2_wizard_footer_mounted', 'ept_post_types', onFooterUpdated );
addAction( 'barn2_wizard_footer_updated', 'ept_post_types', onFooterUpdated );

const onReadyUpdated = ( ready ) => {
	const { key } = ready.props.step;

	if ( 'ept_ready' === key || 'ready' === key ) {
		const heading            = sprintf( ready.props.step.heading, ready.props.getValues().singular ),
			  description        = sprintf( ready.props.step.description, ready.props.getValues().plural ),
			  showSettingsButton = false;
		ready.setState( { heading, description, showSettingsButton } );
	}
}
addAction( 'barn2_wizard_ready_mounted', 'ept_post_types', onReadyUpdated )
addAction( 'barn2_wizard_ready_updated', 'ept_post_types', onReadyUpdated )

const onStepUpdated = ( withForm ) => {
	const query = new URLSearchParams(location.search)

	if ( query.has('action') ) {
		if ( 'ept_name' === withForm.props.step.key ) {
			withForm.setState( { continueButtonText: __( 'Next' ) } )
		}

		if ( 'ept_features' === withForm.props.step.key ) {
			withForm.setState( { continueButtonText: __( 'Create' ) } )
		}
	}
}
addAction( 'barn2_wizard_withform_mounted', 'ept_post_types', onStepUpdated );
addAction( 'barn2_wizard_withform_updated', 'ept_post_types', onStepUpdated );
*/