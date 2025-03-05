import React from 'react';
import './style.css';
// Custom Layout Component
const LayoutCharts = ({ props, item }) => {
    return (
        <article key={item.id}
            className={`tp-divi-popular-post post-${item.id}`}
            data-post-type={`${item.post_type}`}>
            {(props.show_thumbnail === 'on' && item.thumbnail) && (
                <div className='tp-left-wrapper'>
                    <div className='tp-post-thumb'>
                        <img src={item.thumbnail} alt=' ' />
                    </div>
                </div>
            )}
            <div className='tp-right-wrapper'>
                <div className="tp-post-title">
                    <h2><a href={item.permalink}>{item.title}</a></h2>
                </div>
                {(props.show_author === 'on' || props.show_date === 'on' || props.show_comments === 'on' || props.show_views === 'on') && (
                    <div className="tp-meta-data">
                        <p className="post-meta">
                            {props.show_author === 'on' && (
                                <>by <span className="author vcard" dangerouslySetInnerHTML={{ __html: item.meta_author }}></span></>
                            )}
                            {(props.show_date === 'on' && props.show_author === 'on') && (
                                <> | </>
                            )}
                            {props.show_date === 'on' && (
                                <><span className="published" dangerouslySetInnerHTML={{ __html: item.meta_date }}></span></>
                            )}
                            {(props.show_author === 'on' || props.show_date === 'on') && (props.show_comments === 'on') && (
                                <> | </>
                            )}
                            {props.show_comments === 'on' && (
                                <>{item.meta_comments} Comments </>
                            )}
                            {(props.show_views === 'on') && (props.show_author==='on' || props.show_date==='on' || props.show_comments==='on') && (
                                <> | </>
                            )}
                            {props.show_views === 'on' && (
                                <><span className="post_views">{item.meta_views} views</span></>
                            )}
                        </p>
                    </div>
                )}
                {props.show_categories === 'on' && (
                    <div className='tp-post-cats' dangerouslySetInnerHTML={{ __html: item.terms }}>

                    </div>
                )}

                {(props.show_excerpt === 'on') && (
                    <div
                        className="tp-post-content"
                        dangerouslySetInnerHTML={{ __html: item.excerpt }}
                    />
                )}
                
                {props.show_more === 'on' && (
                    <div className='tp-read-more'>
                        <a href={item.permalink}>Read More</a>
                    </div>
                )}
            </div>

        </article>
    );
};


export default LayoutCharts;
