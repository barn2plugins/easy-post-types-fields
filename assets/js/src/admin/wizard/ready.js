import { __, sprintf } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Ready from '../../../../../vendor/barn2/setup-wizard/resources/js/steps/ready';


class EPT_Ready extends Ready {
	getSignposts = () => {
		const values = this.props.getValues();

		return [
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
		];
	}

}

export default EPT_Ready;
