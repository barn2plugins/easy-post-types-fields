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
		const readyStep = barn2_setup_wizard.steps.find( step => step.key === 'ready' )

		setStepOverride( 'ept_features', {
			pageTitle: sprintf( featuresStep.heading, value ),
			pageDescription: sprintf( featuresStep.description, value )
		} )

		setStepOverride( 'ready', {
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

// Disable the popover in the footer.
addFilter( 'barn2_setup_wizard_footer_popover', 'ept-wizard', () => '' )

// Disable the settings button on the last step.
addFilter( 'barn2_setup_wizard_show_settings_button', 'ept-wizard', () => false );

/**
 * Customize the links on the ready step page.
 */
addFilter( 'barn2_setup_wizard_ready_links', 'ept-wizard', ( links, values ) => {

	const customLinks = [
		{
			title: __('Add custom fields'),
			href: `${barn2_setup_wizard.admin_url}admin.php?page=ept_post_types&post_type=ept_${values.slug}&section=fields`,
		},
		{
			title: __('Add taxonomies'),
			href: `${barn2_setup_wizard.admin_url}admin.php?page=ept_post_types&post_type=ept_${values.slug}&section=taxonomies`,
		},
		{
			title: sprintf( __('Add New %s'), values.singular ),
			href: `${barn2_setup_wizard.admin_url}post-new.php?post_type=ept_${values.slug}`,
		},
		{
			title: sprintf( __('Manage Post Types'), values.singular ),
			href: `${barn2_setup_wizard.admin_url}admin.php?page=ept_post_types`,
		}
	]

	return customLinks;

} );

/**
 * Workaround required to send all data that EPT needs.
 */
addFilter( 'barn2_setup_wizard_step_submission_data', 'ept-wizard', ( data, stepValues, datastore, activeStep ) => {
	const newData = {
		...data,
		values: { ...data.values, ...datastore }
	}
	return newData
} )