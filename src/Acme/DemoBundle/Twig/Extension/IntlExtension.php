namespace Acme\Bundle\DemoBundle\Twig;

class IntlExtension extends \Twig_Extension
{

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('intl_day', array($this, 'intlDay')),
            new \Twig_SimpleFilter('intl_number', array($this, 'intlNumber')),
        );
    }

    // Other methods…

    /**
     * NULL locale cause load locale from php.ini
     */
    public function intlNumber($number, $locale = NULL)
    {
        $fmt = new \NumberFormatter($locale, \NumberFormatter::DECIMAL);
        return $fmt->format($number);
    }

    public function getName()
    {
        return 'intl_extension';
    }
}