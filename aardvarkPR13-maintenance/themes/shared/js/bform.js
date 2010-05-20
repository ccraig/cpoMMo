function highlight_form_field(form_field)
{
  if ('undefined' != form_field.className)
  {
    form_field.className += ' highlight';
  }
}

function blur_form_field(form_field)
{
  if ('undefined' != form_field.className)
  {
    if ('highlight' == form_field.className)
    {
      form_field.className = '';
    }
    else
    {
      form_field.className = form_field.className.replace(' highlight', '');
    }
  }
}

function remove_default_input(form_field)
{
  if (form_field.value == form_field.title)
  {
    form_field.value = '';
  }
}

function add_default_input(form_field)
{
  if ('' == form_field.value)
  {
    form_field.value = form_field.title;
  }
}

function disable_submit_buttons(form)
{
  for (i = 0; i < form.elements.length; i++)
  {
    if ('submit' == form.elements[i].type)
    {
      document.getElementById(form.elements[i].id).value = 'Please wait\u2026';
    }
    if ('text' == form.elements[i].type || 'textarea' == form.elements[i].type)
    {
      remove_default_input(document.getElementById(form.elements[i].id));
    }
  }

  return true;
}

function check_textarea_length(textarea_id)
{
  textarea  = document.getElementById(textarea_id);
  maxlength = document.getElementById(textarea.id + '_maxlength');
  counter   = document.getElementById(textarea.id + '_counter');

  if (textarea.value.length <= maxlength.value)
  {
    if (textarea.value == textarea.title)
    {
      counter.value = maxlength.value;
    }
    else
    {
      counter.value = maxlength.value - textarea.value.length;
    }

    previous_string[textarea_id]  = textarea.value;
    alert_shown[textarea_id]      = false;
  }
  else
  {
    if (('undefined' != alert_shown[textarea_id]) && (true != alert_shown[textarea_id]))
    {
      window.alert('You have reached the maximum number of characters (' + maxlength.value + ').');

      alert_shown[textarea_id] = true;
    }

    textarea.value = previous_string[textarea_id];
    textarea.focus();
  }
}

function shrink_textarea(button)
{
  textarea = document.getElementById(button.id.substring(0, button.id.length - 7));
  
  if (5 < textarea.rows)
  {
    textarea.rows -= 5;
  }
}

function expand_textarea(button)
{
  document.getElementById(button.id.substring(0, button.id.length - 7)).rows += 10;
}

window.onload = function()
{
  if (document.getElementById)
  {
    forms = document.getElementsByTagName('form');

    for (i = 0; i < forms.length; i++)
    {
      forms[i].onsubmit = function() {disable_submit_buttons(this);}
    }

    inputs = document.getElementsByTagName('input');

    for (i = 0; i < inputs.length; i++)
    {
      if ('text' == inputs[i].type)
      {
        inputs[i].onfocus   = function() {highlight_form_field(this); remove_default_input(this);}
        inputs[i].onblur  = function() {blur_form_field(this); add_default_input(this);}

        if ('' == inputs[i].value)
        {
          add_default_input(inputs[i]);
        }
      }
      else if ('password' == inputs[i].type)
      {
        inputs[i].onfocus   = function() {highlight_form_field(this);}
        inputs[i].onblur  = function() {blur_form_field(this);}
      }
    }

    textareas = document.getElementsByTagName('textarea');

    var textarea_timer = new Array();

    for (i = 0; i < textareas.length; i++)
    {
      textareas[i].onfocus  = function() {highlight_form_field(this); remove_default_input(this);}
      textareas[i].onblur   = function() {blur_form_field(this); add_default_input(this);}

      if ('' == textareas[i].value)
      {
        add_default_input(textareas[i]);
      }

      if (document.createElement)
      {
        textarea_toolbar = document.createElement('div');
        textarea_toolbar.className = 'textarea_toolbar';

        if (document.getElementById(textareas[i].id + '_maxlength'))
        {
          previous_string = new Array();
          alert_shown     = new Array();

          textarea_timer[i] = setInterval('check_textarea_length(\'' + textareas[i].id + '\')', 500);

          counter = document.createElement('input');
          counter.setAttribute('id', textareas[i].id + '_counter');
          counter.setAttribute('type', 'text');
          counter.setAttribute('readonly', 'readonly');
          counter.setAttribute('size', '4');
          counter.setAttribute('title', 'characters remaining');
          counter.setAttribute('value', '0');

          textarea_toolbar.appendChild(counter);
        }

        expand_button = document.createElement('input');
        expand_button.setAttribute('type', 'button');
        expand_button.setAttribute('id', textareas[i].id + '_expand');
        expand_button.setAttribute('value', '+');
        expand_button.setAttribute('title', 'increase height of textarea');
        expand_button.onclick = function () {expand_textarea(this);}

        shrink_button = document.createElement('input');
        shrink_button.setAttribute('type', 'button');
        shrink_button.setAttribute('id', textareas[i].id + '_shrink');
        shrink_button.setAttribute('value', '-');
        shrink_button.setAttribute('title', 'decrease height of textarea');
        shrink_button.onclick = function () {shrink_textarea(this);}

        textarea_toolbar.appendChild(expand_button);
        textarea_toolbar.appendChild(shrink_button);

        textareas[i].parentNode.insertBefore(textarea_toolbar, textareas[i]);

      }
    }
  }
}
