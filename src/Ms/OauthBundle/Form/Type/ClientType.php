<?php

namespace Ms\OauthBundle\Form\Type;

use \Symfony\Component\Form\AbstractType;
use \Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of ClientType
 *
 * @author user
 */
class ClientType extends UserType {

    /**
     * @inheridoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('appTitle', 'text', array('max_length' => 120))
                ->add('redirectionUri', 'text')
                ->add('clientType',
                    'choice',
                    array(
                        'choices' => array(
                            '1' => 'Confidential', 
                            '0' => 'Public'
                        )
                    )
                );
        parent::buildForm($builder, $options);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'ms_oauthbundle_client';
    }

    /**
     * 
     * @inheridoc
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(array(
            'data_class' => 'Ms\OauthBundle\Entity\Client'
        ));
    }
}
