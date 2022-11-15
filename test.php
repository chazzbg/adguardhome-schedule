<?php

file_put_contents(
    '/home/chazzbg/PhpstormProjects/adguardhome-scheduler/as',
    file_get_contents('/home/chazzbg/PhpstormProjects/adguardhome-scheduler/as') . (new \DateTime())->format(
        'Y-M-d_H:i:s'
    ) . PHP_EOL
);
sleep(2);