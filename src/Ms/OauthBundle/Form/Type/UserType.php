<?php

namespace Ms\OauthBundle\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symfony\Component\Form\FormBuilderInterface;
/**
 * Description of UserType
 *
 * @author user
 */
class UserType extends AbstractType {
    
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('email', 'email', array('label' => 'e-mail'))
                ->add('Submit', 'submit', array('label' => 'Εγγραφή'));
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'ms_oauthbundle_user';
    }
    
    /**
     * @inheritdoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Ms\OauthBundle\Entity\User'
        ));
    }
}
