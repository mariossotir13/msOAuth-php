<?php

namespace Ms\OauthBundle\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of ResourceOwnerType
 *
 * @author Marios
 */
class ResourceOwnerType extends UserType {
    
    /**
     * @inheridoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('id', 'text', array('max_length' => 43))
                ->add('password', 'password');
        parent::buildForm($builder, $options);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'ms_oauthbundle_user';
    }

    /**
     * 
     * @inheridoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Ms\OauthBundle\Entity\User'
        ));
    }
}
