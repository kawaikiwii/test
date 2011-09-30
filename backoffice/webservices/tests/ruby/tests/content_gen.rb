require '../test_case'

class WCMContentGenerationWebServiceTestCase < Test::Unit::TestCase
  include WCMWebServiceTestCase

  def setup
    super
    @session_token = wcm.login('admin', encrypt('admin'), 'en')
  end

  def tear_down
    unless @session_token.nil?
      wcm.logout(@session_token)
      @session_token = nil
    end
    super
  end

  attr_reader :session_token

#   def test_generate
#   rescue => error
#   end

#   def test_generate_content
#   rescue => error
#   end

  def test_generate_template_content
    wcm_om = wcm('ObjectManagement')

    category_id = wcm_om.createObject(session_token, 'wcmTemplateCategory', '<wcmTemplateCategory/>')
    template_id = wcm_om.createObject(session_token, 'wcmTemplate', '<wcmTemplate/>')

    #wcm.generateTemplateContent(session_token, template_id, [], false)
  rescue => error
    flunk error
  ensure
    wcm_om.deleteObject(session_token, 'wcmTemplate', template_id)         if template_id.to_i != 0
    wcm_om.deleteObject(session_token, 'wcmTemplateCategory', category_id) if category_id.to_i != 0
  end

#   def test_generate_object_content
#     wcm_om = wcm('BusinessObjectManagement')

#     object_id, object_xml = create_test_object(wcm_om)

#     wcm.generateObjectContent(session_token, 'article', object_id, false, false)
#   rescue => error
#     flunk error
#   ensure
#     wcm_om.deleteObject(session_token, 'article', object_id) if object_id.to_i != 0
#   end
end
