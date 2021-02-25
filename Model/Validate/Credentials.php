<?php
declare(strict_types=1);

namespace Anyday\PaymentAndTrack\Model\Validate;

use Anyday\PaymentAndTrack\Api\Anyday\ManagerInterface;
use Anyday\PaymentAndTrack\Api\Validate\CredentialsInterface;

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
