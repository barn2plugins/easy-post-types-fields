import { __ } from '@wordpress/i18n';
import { addAction, addFilter } from '@wordpress/hooks';
import { has, get, isEmpty } from 'lodash';
import axios from "axios";
import qs from 'qs';

import GridiconChevronRight from 'gridicons/dist/chevron-right';
import { Fill, CardBody } from '@wordpress/components';
import { List } from '@woocommerce/components';


import { SetupWizardSettings, getParsedAjaxError } from '../../../../../vendor/barn2/setup-wizard/resources/js/utilities'

import Welcome from './welcome';

const addCustomSteps = ( steps ) => {
    const customSteps = [
        { key: 'welcome', component: Welcome }
    ];

    for ( const { key, component } of customSteps ) {
        const index = steps.indexOf( steps.find( s => key === s.key ) );
        if ( -1 !== index ) {
            steps[ index ].container = component;
        }
    }

    return steps;
}
addFilter( 'barn2_setup_wizard_steps', 'bulk-variations', addCustomSteps );


const onRestart = ( wizard ) => {

	wizard.setState( { wizard_loading: true, wizard_complete: true } );

	const actions = {
		'setup': [ 'welcome', 'more' ],
		'add': [ 'ready' ]
	};

	const action = barn2_setup_wizard.action
	if ( actions[ action ] ) {
		let firstStep = true
		for ( const step of wizard.state.steps ) {
			if ( actions[ action ].indexOf( step.key ) !== -1 ) {
				wizard.showStep( step.key )
				if ( firstStep ) {
					wizard.goToStep( step.key )
					firstStep = false
				}
			} else {
				wizard.hideStep( step.key )
			}
		}
	}

	wizard.setState( { wizard_loading: false } );
}
addAction( 'barn2_wizard_on_restart', 'easy-post-types-fields', onRestart );

const readyPageContent = () => {
	return ( props ) => {
		const { step } = props;

		const listItems = [
			{
				after: <GridiconChevronRight />,
				title: __('Add custom fields or taxonomies'),
				href: `${barn2_setup_wizard.admin_url}admin.php?page=ept_post_types`,
			},
			{
				after: <GridiconChevronRight />,
				title: __('Go to All %s').replace( '%s', step.fields.name.value ),
				href: `${barn2_setup_wizard.admin_url}edit.php?post_type=${step.fields.post_type.value}`,
			},
			{
				after: <GridiconChevronRight />,
				title: __('Add New %s').replace( '%s', step.fields.singular_name.value ),
				href: `${barn2_setup_wizard.admin_url}post-new.php?post_type=${step.fields.post_type.value}`,
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