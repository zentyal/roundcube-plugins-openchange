<?php
$CONTACT_COLTYPES = array(
        'name'         => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('name'), 'category' => 'main'),
        'firstname'    => array('type' => 'text', 'size' => 19, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('firstname'), 'category' => 'main'),
        'surname'      => array('type' => 'text', 'size' => 19, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('surname'), 'category' => 'main'),
        'email'        => array('type' => 'text', 'size' => 40, 'maxlength' => 254, 'label' => rcube_label('email'), 'subtypes' => array('home','work','other'), 'category' => 'main'),
        'middlename'   => array('type' => 'text', 'size' => 19, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('middlename'), 'category' => 'main'),
        'prefix'       => array('type' => 'text', 'size' => 8,  'maxlength' => 20, 'limit' => 1, 'label' => rcube_label('nameprefix'), 'category' => 'main'),
        'suffix'       => array('type' => 'text', 'size' => 8,  'maxlength' => 20, 'limit' => 1, 'label' => rcube_label('namesuffix'), 'category' => 'main'),
        'nickname'     => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('nickname'), 'category' => 'main'),
        'jobtitle'     => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('jobtitle'), 'category' => 'main'),
        'organization' => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('organization'), 'category' => 'main'),
        'department'   => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('department'), 'category' => 'main'),
        'gender'       => array('type' => 'select', 'limit' => 1, 'label' => rcube_label('gender'), 'options' => array('male' => rcube_label('male'), 'female' => rcube_label('female')), 'category' => 'personal'),
        'maidenname'   => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('maidenname'), 'category' => 'personal'),
        'phone'        => array('type' => 'text', 'size' => 40, 'maxlength' => 20, 'label' => rcube_label('phone'), 'subtypes' => array('home','home2','work','work2','mobile','main','homefax','workfax','car','pager','video','assistant','other'), 'category' => 'main'),
        'address'      => array('type' => 'composite', 'label' => rcube_label('address'), 'subtypes' => array('home','work','other'), 'childs' => array(
                'street'     => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'label' => rcube_label('street'), 'category' => 'main'),
                'locality'   => array('type' => 'text', 'size' => 28, 'maxlength' => 50, 'label' => rcube_label('locality'), 'category' => 'main'),
                'zipcode'    => array('type' => 'text', 'size' => 8,  'maxlength' => 15, 'label' => rcube_label('zipcode'), 'category' => 'main'),
                'region'     => array('type' => 'text', 'size' => 12, 'maxlength' => 50, 'label' => rcube_label('region'), 'category' => 'main'),
                'country'    => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'label' => rcube_label('country'), 'category' => 'main'),
                ), 'category' => 'main'),
        'birthday'     => array('type' => 'date', 'size' => 12, 'maxlength' => 16, 'label' => rcube_label('birthday'), 'limit' => 1, 'render_func' => 'rcmail_format_date_col', 'category' => 'personal'),
        'anniversary'  => array('type' => 'date', 'size' => 12, 'maxlength' => 16, 'label' => rcube_label('anniversary'), 'limit' => 1, 'render_func' => 'rcmail_format_date_col', 'category' => 'personal'),
        'website'      => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'label' => rcube_label('website'), 'subtypes' => array('homepage','work','blog','profile','other'), 'category' => 'main'),
        'im'           => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'label' => rcube_label('instantmessenger'), 'subtypes' => array('aim','icq','msn','yahoo','jabber','skype','other'), 'category' => 'main'),
        'notes'        => array('type' => 'textarea', 'size' => 40, 'rows' => 15, 'maxlength' => 500, 'label' => rcube_label('notes'), 'limit' => 1),
        'photo'        => array('type' => 'image', 'limit' => 1, 'category' => 'main'),
        'assistant'    => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('assistant'), 'category' => 'personal'),
        'manager'      => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('manager'), 'category' => 'personal'),
        'spouse'       => array('type' => 'text', 'size' => 40, 'maxlength' => 50, 'limit' => 1, 'label' => rcube_label('spouse'), 'category' => 'personal'),
?>
