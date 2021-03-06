<?php
/**
 * Display a list of users to delete in bulk.
 *
 * Also used to show the search by domain results
 */

// Are we performing a search
$limit = get_input('limit', 30);
$offset = get_input('offset', 0);
$domain = get_input('domain');
$title = '';

if ($domain) {
	$title = elgg_echo('bulk_user_admin:title:domains', array($domain));
}

$options = array(
	'type' => 'user',
	'limit' => $limit,
	'offset' => $offset,
	'full_view' => false
);

if ($domain) {
	$users = bulk_user_admin_get_users_by_email_domain($domain, $options);
	$options['count'] = true;
	$users_count = bulk_user_admin_get_users_by_email_domain($domain, $options);
} else {
	$users = elgg_get_entities($options);
	$options['count'] = true;
	$users_count = elgg_get_entities($options);
}

$pagination = elgg_view('navigation/pagination', array(
	'base_url' => current_page_url(),
	'offset' => $offset,
	'count' => $users_count,
	'limit' => $limit
));

$form_vars = array(
	'users' => $users,
);

$form = elgg_view_form('bulk_user_admin/delete', array(), $form_vars);

$domain_form = '';

if ($domain) {
	$delete_button = elgg_view('input/submit', array(
		'value' => elgg_echo('bulk_user_admin:delete:domainall'),
		'class' => 'mtm elgg-button elgg-button-submit elgg-requires-confirmation'
	));

	$hidden = elgg_view('input/hidden', array(
		'name' => 'domain',
		'value' => $domain
	));

	$form_body = $delete_button . $hidden;

	$domain_form = elgg_view('input/form', array(
		'action' =>  elgg_get_site_url() . 'action/bulk_user_admin/delete_by_domain',
		'body' => $form_body
	));

}

$summary = "<div>" . elgg_echo('bulk_user_admin:usersfound', array($users_count)) . "</div>";

if ($domain) {
	$summary .= '<br />';
	$summary .= elgg_view('output/url', array(
		'href' => elgg_http_remove_url_query_element(current_page_url(), 'domain'),
		'text' => elgg_echo('bulk_user_admin:allusers')
	));
}

elgg_set_context('admin');

echo $title . $summary . $pagination . $form . $domain_form . $pagination;
