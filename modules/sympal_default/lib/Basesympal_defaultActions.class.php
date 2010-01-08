<?php

class Basesympal_defaultActions extends sfActions
{
  public function executeSitemap(sfWebRequest $request)
  {
    $this->setLayout(false);
    $this->sitemapGenerator = new sfSympalSitemapGenerator($this->getContext()->getConfiguration()->getApplication());
  }

  public function executeNew_site(sfWebRequest $request)
  {
    $this->useSiteTheme();
  }

  public function executeChange_language(sfWebRequest $request)
  {
    $oldCulture = $this->getUser()->getCulture();
    $this->form = new sfFormLanguage($this->getUser(), array('languages' => sfSympalConfig::get('language_codes', null, array($this->getUser()->getCulture()))));
    unset($this->form[$this->form->getCSRFFieldName()]);

    $this->form->process($request);

    $newCulture = $this->getUser()->getCulture();

    $this->getUser()->setFlash('notice', 'Changed language successfully!');

    return $this->redirect(str_replace('/'.$oldCulture.'/', '/'.$newCulture.'/', $this->getUser()->getReferer('@homepage')));
  }

  public function executeAsk_confirmation(sfWebRequest $request)
  {
    if ($this->isAjax())
    {
      $this->setLayout(false);
    } else {
      $this->useAdminTheme();
    }

    $this->url = $request->getUri();
    $this->title = $request->getAttribute('title');
    $this->message = $request->getAttribute('message');
    $this->isAjax = $request->getAttribute('is_ajax');
  }

  public function executeError404()
  {
    $this->useSiteTheme();
  }

  public function executeDisabled()
  {
    $this->useSiteTheme();
  }
}