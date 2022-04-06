console.log( 'wizard-custom start');

import { __, sprintf } from '@wordpress/i18n';
import { addAction, addFilter } from '@wordpress/hooks';

import GridiconChevronRight from 'gridicons/dist/chevron-right';
import { Fill, CardBody } from '@wordpress/components';
import { List } from '@woocommerce/components';

import Welcome from './welcome';

const addCustomSteps = ( steps ) => {
    const customSteps = [
        { key: 'welcome', component: Welcome },
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
	if ( -1 !== [ 'ept_name', 'ept_features' ].indexOf( footer.props.step.key ) ) {
		footer.setState( { isSkipTooltipVisible: false, skipText: __( 'Cancel' ) } )
	}

	if ( 'ept_ready' === footer.props.step.key ) {
		footer.setState( { isSkipVisible: false } )
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
	console.log( 'this is the withForm component', withForm );
	if ( 'ept_name' === withForm.props.step.key ) {
		withForm.setState( { continueButtonText: __( 'Next' ) } )
	}

	if ( 'ept_features' === withForm.props.step.key ) {
		withForm.setState( { continueButtonText: __( 'Create' ) } )
	}

}
addAction( 'barn2_wizard_withform_mounted', 'ept_post_types', onStepUpdated );
addAction( 'barn2_wizard_withform_updated', 'ept_post_types', onStepUpdated );


const readyPageContent = () => {
	return ( props ) => {
		const values = props.getValues();

		const listItems = [
			{
				after: <GridiconChevronRight />,
				title: __('Add custom fields or taxonomies'),
				href: `${barn2_setup_wizard.admin_url}admin.php?page=ept_post_types`,
			},
			{
				after: <GridiconChevronRight />,
				title: sprintf( __('Add New %s'), values.singular ),
				href: `${barn2_setup_wizard.admin_url}post-new.php?post_type=ept_${values.slug}`,
			},
			{
				after: <GridiconChevronRight />,
				title: __('Manage your post types'),
				href: `${barn2_setup_wizard.admin_url}admin.php?page=ept_post_types`,
			}
		];

		return (
			<Fill name="ReadyPageContent">
				<CardBody>
					<List items={listItems} />
				</CardBody>
			</Fill>
		);
	};
}
addFilter('barn2_setup_wizard_ready_page', 'ept_post_types', readyPageContent);