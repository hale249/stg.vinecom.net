<?php

namespace App\Constants;

class Status
{

    const ENABLE = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO = 0;

    const VERIFIED = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS = 1;
    const PAYMENT_PENDING = 2;
    const PAYMENT_REJECT = 3;

    const INVEST_PAYMENT_PENDING = 0;

    const TICKET_OPEN = 0;
    const TICKET_ANSWER = 1;
    const TICKET_REPLY = 2;
    const TICKET_CLOSE = 3;

    const PRIORITY_LOW = 1;
    const PRIORITY_MEDIUM = 2;
    const PRIORITY_HIGH = 3;

    const USER_ACTIVE = 1;
    const USER_BAN = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING = 2;
    const KYC_VERIFIED = 1;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM = 3;

    const PAYMENT_ONLINE = 1;
    const PAYMENT_WALLET = 2;

    const INVEST_PENDING = 0;
    const INVEST_PENDING_ADMIN_REVIEW = 5;
    const INVEST_ACCEPT = 1;
    const INVEST_RUNNING = 2;
    const INVEST_COMPLETED = 3;
    const INVEST_CLOSED = 4;
    const INVEST_CANCELED = 9;


    const CAPITAL_BACK = 1;

    const LIFETIME = -1;
    const REPEAT = 2;

    const PROJECT_CONFIRMED = 1;
    const PROJECT_END = 2;
    const PROJECT_FEATURED = 1;
}
