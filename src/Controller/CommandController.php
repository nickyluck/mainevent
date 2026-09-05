<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;


class CommandController extends AbstractController
{
    #[Route('/command/cache/clear', name: 'cache_clear')]
    public function cache_clear(KernelInterface $kernel)
    {
        return $this->do_command($kernel, 'cache:clear');
    }

    private function do_command($kernel, $command)
    {
        $env = $kernel->getEnvironment();

        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => $command,
            '--env' => $env
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();
        $this->addFlash('success', $content);

        return $this->redirectToRoute('home');
    }
}
