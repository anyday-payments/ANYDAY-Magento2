<?php
declare(strict_types=1);

namespace Anyday\Payment\Api\Validate;

interface CredentialsInterface
{
    /**
     * Validate Credentials
     *
     * @param string $data
     * @return string
     */
    public function validate($data);
}
