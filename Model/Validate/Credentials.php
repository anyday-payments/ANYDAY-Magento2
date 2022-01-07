<?php
declare(strict_types=1);

namespace Anyday\Payment\Model\Validate;

use Anyday\Payment\Api\Anyday\ManagerInterface;
use Anyday\Payment\Api\Validate\CredentialsInterface;

class Credentials implements CredentialsInterface
{
    /**
     * @var ManagerInterface
     */
    private $managerAnyday;

    /**
     * Credentials constructor.
     *
     * @param ManagerInterface $managerAnyday
     */
    public function __construct(
        ManagerInterface $managerAnyday
    ) {
        $this->managerAnyday    = $managerAnyday;
    }

    /**
     * Validate Credentials
     *
     * @param string $data
     * @return mixed|string
     */
    public function validate($data)
    {
        return $this->managerAnyday->getCredentialsFromAnyday($data);
    }
}
