<?php


function om_ext_forms_token_info() {
  $type = array(
    'name' => t('External Forms'), 
    'description' => t('Tokens related to external forms.'), 
  );
  $form = array();
  $form['name'] = array(
    'name' => t('Form Name'), 
    'description' => t('The name of the form'),
  );
  return array(
    'types' => array('om-ext-forms-ext-form' => $type), 
    'tokens' => array('om-ext-forms-ext-form' => $form),
  );
}

function om_ext_forms_tokens($type, $tokens, array $data, array $options, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata) {
  $replacements = array();
  if ($type == 'om-ext-forms-ext-form' && !empty($data['om_ext_forms_ext_form'])) {
    foreach ($tokens as $name => $original) {
      switch ($name) {
        case 'name':
          $name = $data['om_ext_forms_ext_form']->get('name')->getValue();
          $replacements[$original] = $name[0]['value'];
          break;
      }
    }
  }
  return $replacements;
}
