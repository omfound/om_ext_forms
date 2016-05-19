<?php

/**
 * @file
 * Test cases for OM Ext Forms Module.
 */

namespace Drupal\om_ext_forms\Tests;

use Drupal\om_ext_forms\Entity\ExtForm;
use Drupal\examples\Tests\ExamplesTestBase;

/**
 * Tests the basic functions of the OM Ext Forms module.
 *
 * @package Drupal\om_ext_forms\Tests
 *
 * @ingroup om_ext_forms
 *
 * @group om_ext_forms
 * @group examples
 */
class ContentEntityExampleTest extends ExamplesTestBase {

  public static $modules = array('om_ext_forms', 'block', 'field_ui');

  /**
   * Basic tests for OM Ext Forms.
   */
  public function testContentEntityExample() {
    $web_user = $this->drupalCreateUser(array(
      'add ext form entity',
      'edit ext form entity',
      'view ext form entity',
      'delete ext form entity',
      'administer ext form entity',
      'administer om_ext_forms_ext_form display',
      'administer om_ext_forms_ext_form fields',
      'administer om_ext_forms_ext_form form display',
    ));

    // Anonymous User should not see the link to the listing.
    $this->assertNoText(t('Ext Forms Listing'));

    $this->drupalLogin($web_user);

    // Web_user user has the right to view listing.
    $this->assertLink(t('Ext Forms Listing'));

    $this->clickLink(t('Ext Forms Listing'));

    // WebUser can add entity content.
    $this->assertLink(t('Add Ext Form'));

    $this->clickLink(t('Add Ext Form'));

    $this->assertFieldByName('name[0][value]', '', 'Name Field, empty');
    $this->assertFieldByName('name[0][value]', '', 'First Name Field, empty');
    $this->assertFieldByName('name[0][value]', '', 'Gender Field, empty');

    $user_ref = $web_user->name->value . ' (' . $web_user->id() . ')';
    $this->assertFieldByName('user_id[0][target_id]', $user_ref, 'User ID reference field points to web_user');

    // Post content, save an instance. Go back to list after saving.
    $edit = array(
      'form_link[0][value]' => 'http://www.testlink.com',
    );
    $this->drupalPostForm(NULL, $edit, t('Save'));

    // Entity listed.
    $this->assertLink(t('Edit'));
    $this->assertLink(t('Delete'));

    $this->clickLink('test name');

    // Entity shown.
    $this->assertText(t('test name'));
    $this->assertText(t('test first name'));
    $this->assertText(t('male'));
    $this->assertLink(t('Add Ext Form'));
    $this->assertLink(t('Edit'));
    $this->assertLink(t('Delete'));

    // Delete the entity.
    $this->clickLink('Delete');

    // Confirm deletion.
    $this->assertLink(t('Cancel'));
    $this->drupalPostForm(NULL, array(), 'Delete');

    // Back to list, must be empty.
    $this->assertNoText('test name');

    // Settings page.
    $this->drupalGet('admin/structure/om_ext_forms_ext_form_settings');
    $this->assertText(t('Ext Form Settings'));

    // Make sure the field manipulation links are available.
    $this->assertLink(t('Settings'));
    $this->assertLink(t('Manage fields'));
    $this->assertLink(t('Manage form display'));
    $this->assertLink(t('Manage display'));
  }

  /**
   * Test all paths exposed by the module, by permission.
   */
  public function testPaths() {
    // Generate a ext form so that we can test the paths against it.
    $ext_form = ExtForm::create(
      array(
        'form_link' => 'http://www.testlink.com',
      )
    );
    $ext_form->save();

    // Gather the test data.
    $data = $this->providerTestPaths($ext_form->id());

    // Run the tests.
    foreach ($data as $datum) {
      // drupalCreateUser() doesn't know what to do with an empty permission
      // array, so we help it out.
      if ($datum[2]) {
        $user = $this->drupalCreateUser(array($datum[2]));
        $this->drupalLogin($user);
      }
      else {
        $user = $this->drupalCreateUser();
        $this->drupalLogin($user);
      }
      $this->drupalGet($datum[1]);
      $this->assertResponse($datum[0]);
    }
  }

  /**
   * Data provider for testPaths.
   *
   * @param int $ext_form_id
   *   The id of an existing Ext Form entity.
   *
   * @return array
   *   Nested array of testing data. Arranged like this:
   *   - Expected response code.
   *   - Path to request.
   *   - Permission for the user.
   */
  protected function providerTestPaths($ext_form_id) {
    return array(
      array(
        200,
        '/om_ext_forms_ext_form/' . $ext_form_id,
        'view ext form entity',
      ),
      array(
        403,
        '/om_ext_forms_ext_form/' . $ext_form_id,
        '',
      ),
      array(
        200,
        '/om_ext_forms_ext_form/list',
        'view ext form entity',
      ),
      array(
        403,
        '/om_ext_forms_ext_form/list',
        '',
      ),
      array(
        200,
        '/om_ext_forms_ext_form/add',
        'add ext form entity',
      ),
      array(
        403,
        '/om_ext_forms_ext_form/add',
        '',
      ),
      array(
        200,
        '/om_ext_forms_ext_form/' . $ext_form_id . '/edit',
        'edit ext form entity',
      ),
      array(
        403,
        '/om_ext_forms_ext_form/' . $ext_form_id . '/edit',
        '',
      ),
      array(
        200,
        '/ext_form/' . $ext_form_id . '/delete',
        'delete ext form entity',
      ),
      array(
        403,
        '/ext_form/' . $ext_form_id . '/delete',
        '',
      ),
      array(
        200,
        'admin/structure/om_ext_forms_ext_form_settings',
        'administer ext form entity',
      ),
      array(
        403,
        'admin/structure/om_ext_forms_ext_form_settings',
        '',
      ),
    );
  }

  /**
   * Test add new fields to the ext form entity.
   */
  public function testAddFields() {
    $web_user = $this->drupalCreateUser(array(
      'administer ext form entity',
      'administer om_ext_forms_ext_form display',
      'administer om_ext_forms_ext_form fields',
      'administer om_ext_forms_ext_form form display',
    ));

    $this->drupalLogin($web_user);
    $entity_name = 'om_ext_forms_ext_form';
    $add_field_url = 'admin/structure/' . $entity_name . '_settings/fields/add-field';
    $this->drupalGet($add_field_url);
    $field_name = 'test_name';
    $edit = array(
      'new_storage_type' => 'list_string',
      'label' => 'test name',
      'field_name' => $field_name,
    );

    $this->drupalPostForm(NULL, $edit, t('Save and continue'));
    $expected_path = $this->buildUrl('admin/structure/' . $entity_name . '_settings/fields/' . $entity_name . '.' . $entity_name . '.field_' . $field_name . '/storage');

    // Fetch url without query parameters.
    $current_path = strtok($this->getUrl(), '?');
    $this->assertEqual($expected_path, $current_path, 'It should redirect to field storage settings page.');

  }

}
