require 'rexml/document'

require '../test_case'

class WCMBusinessSearchWebServiceTestCase < Test::Unit::TestCase
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

  def test_search
    wcm_om = wcm('BusinessObjectManagement')

    object_id1, object_xml1 = create_test_object(wcm_om, 'article', 'title' => 'The Sky is Blue!')
    object_id2, object_xml2 = create_test_object(wcm_om, 'article', 'title' => 'The Universe is Gone!')
    object_id3, object_xml3 = create_test_object(wcm_om, 'article', 'title' => 'The Earth is Round and the Sky is Blue!')

    params = [
      WCMWebServiceNameValuePair.new('className', 'article'),
      WCMWebServiceNameValuePair.new('title', 'sky is blue')
    ]
    num_found = wcm.search(session_token, 'test_search', params)
    assert_equal 2, num_found

    object_xmls = wcm.getSearchResults(session_token, 'test_search', 0, num_found - 1)
    if object_xmls[0] =~ %r(The Earth is Round)
      assert_objects_equal object_xml3, object_xmls[0]
      assert_objects_equal object_xml1, object_xmls[1]
    else
      assert_objects_equal object_xml1, object_xmls[0]
      assert_objects_equal object_xml3, object_xmls[1]
    end
  rescue => error
    flunk error
  ensure
    wcm_om.deleteObject(session_token, 'article', object_id1) if object_id1.to_i != 0
    wcm_om.deleteObject(session_token, 'article', object_id2) if object_id2.to_i != 0
    wcm_om.deleteObject(session_token, 'article', object_id3) if object_id3.to_i != 0
  end
end
