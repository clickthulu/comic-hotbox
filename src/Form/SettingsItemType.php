<?php

namespace App\Form;

use App\Entity\Settings;
use App\Enumerations\OptionEnumerationInterface;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Rule\Rule;
use Sabberworm\CSS\RuleSet\AtRuleSet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                [$this, 'onPostSetData']
            )
        ;

    }

    public function onPostSetData(FormEvent $event): void
    {
        $form = $event->getForm();
        /**
         * @var Settings $data
         */
        $data = $event->getData();
        $options = $this->getOptions($data);
        switch($data->getType()) {
            case Settings::TYPE_INT:
            case Settings::TYPE_INTEGER:
                $options['attr']['class'] = 'form-control';
                $form->add(
                    'value',
                    NumberType::class,
                    $options
                );
                break;
            case Settings::TYPE_BOOL:
            case Settings::TYPE_BOOLEAN:
                $options['multiple'] = false;
                $options['expanded'] = false;
                $options['choices'] = [
                    'Yes' => 1,
                    'No' => 0
                ];
                $options['attr'] = ['class' => 'form-select'];
                $options['choice_attr'] = [
                    'class' => 'col'
                ];
                $form->add(
                    'value',
                    ChoiceType::class,
                    $options
                );
                break;
            case Settings::TYPE_ARRAY:
                $source = $data->getSourceoptions();
                $source = explode(',', $source);
                $optionvalues = array_map('trim', $source);
                $ovals = [
                    '' => ''
                ];
                foreach ($optionvalues as $ov) {
                    $ovals[$ov] = $ov;
                }
                $options['multiple'] = false;
                $options['expanded'] = false;
                $options['choices'] = $ovals;
                $options['attr'] = ['class' => 'form-select'];
                $options['choice_attr'] = [
                    'class' => 'col'
                ];
                $form->add(
                    'value',
                    ChoiceType::class,
                    $options
                );
                break;
            case Settings::TYPE_FILESELECT:
                $source = $data->getSourceoptions();
                $base = __DIR__ . "/../..";
                $webpath = "{$base}/public";
                $optionvalues = glob("{$webpath}/{$source}");
                $ovals = [
                    'Default' => ' '
                ];
                foreach ($optionvalues as $ov) {
                    $baseov = basename($ov);
                    $pathov = str_replace($webpath, '', $ov);
                    $ovals[$baseov] = $pathov;
                }

                $options['multiple'] = false;
                $options['expanded'] = false;
                $options['choices'] = $ovals;
                $options['attr'] = ['class' => 'form-select'];
                $options['choice_attr'] = [
                    'class' => 'col'
                ];
                $form->add(
                    'value',
                    ChoiceType::class,
                    $options
                );
                break;
            case Settings::TYPE_CSSSELECT:
                $source = $data->getSourceoptions();
                $base = __DIR__ . "/../..";
                $webpath = "{$base}/public";
                $optionvalues = glob("{$webpath}/{$source}");
                $ovals = [
                    'Default' => ' '
                ];
                foreach ($optionvalues as $ov) {
                    $baseov = basename($ov);
                    $pathov = str_replace($webpath, '', $ov);
                    $cssParser = new Parser(file_get_contents($ov));
                    $cssDoc = $cssParser->parse();
                    $rulesets = $cssDoc->getAllRuleSets();
                    foreach ($rulesets as $ruleset) {
                        if (is_a($ruleset, AtRuleSet::class)) {
                            if ($ruleset->atRuleName() === 'property' && $ruleset->atRuleArgs() === 'theme') {
                                /**
                                 * @var Rule $rule
                                 */
                                $rule = $ruleset->getRules('syntax')[0];
                                $baseov = trim((string)$rule->getValue(), "\'\"\n\r\t ");
                                $foo = 'bar';
                            }
                        }
                    }

                    $ovals[$baseov] = $pathov;
                }

                $options['multiple'] = false;
                $options['expanded'] = false;
                $options['choices'] = $ovals;
                $options['attr'] = ['class' => 'form-select'];
                $options['choice_attr'] = [
                    'class' => 'col'
                ];
                $form->add(
                    'value',
                    ChoiceType::class,
                    $options
                );
                break;
            case Settings::TYPE_ENUMERATION:
                $source = $data->getSourceoptions();
                /**
                 * @var OptionEnumerationInterface $enum
                 */
                $enum = new $source;
                $optionvalues = $enum->toArray();
                $options['multiple'] = false;
                $options['expanded'] = false;
                $options['choices'] = $optionvalues;
                $options['attr'] = ['class' => 'form-select'];
                $options['choice_attr'] = [
                    'class' => 'col'
                ];
                $form->add(
                    'value',
                    ChoiceType::class,
                    $options
                );
                break;
            case Settings::TYPE_STRING:
            default:
                $form->add(
                    'value',
                    TextType::class,
                    $options
                );
                break;
        }



        $foo = 'bar';
    }

    protected function getOptions(Settings $settings)
    {
        return [
            'label' => $settings->getDisplayName() . ":",
            'attr' => ['class' => 'form-control'],
            'label_attr' => [
                'class' => 'col-4 text-end text-bold'
            ],
            'help' => $settings->getHelp()
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Settings::class
        ]);
    }

}