<?php
/**
 * @file
 * Contains Drupal\om_ext_forms\Form\ExtFormForm.
 */

namespace Drupal\om_ext_forms\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the om_ext_forms entity edit forms.
 *
 * @ingroup om_ext_forms
 */
class ExtFormForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\om_ext_forms\Entity\ExtForm */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->getUntranslated()->language()->getId(),
      '#languages' => Language::STATE_ALL,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.om_ext_forms_ext_form.collection');
    $entity = $this->getEntity();
    $entity->save();
  }

}
